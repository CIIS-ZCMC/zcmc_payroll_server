# General Payroll — 7-Step Process Plan

Scope: the **general payroll** run, executed **separately for Regular and Job Order**.
Out of scope by design: night differential, 13th month, and other special payrolls — those
have their own `payroll_type` and their own process rows.

This plan is written against the code as it stands on `refactor/payroll-consolidation`.
Section 9 lists the defects and gaps found while reading it; several of them are
prerequisites for the steps below, not optional cleanups.

---

## 0. How the run is scoped

A run is identified by **`payroll_processes(payroll_period_id, payroll_type)`** — already
unique per that pair (`2026_02_13_182217_create_payroll_processes_table.php`).

- **Regular vs Job Order** is carried by `payroll_periods.employment_type`, and the set of
  employees in a run is whoever has an `employee_time_records` row for that period. So the
  two runs are naturally disjoint: separate `PayrollPeriod` row → separate `PayrollProcess`
  row → separate step counter → separate lock.
- `payroll_type` on the process stays `PayrollType::REGULAR (0)` / `PayrollType::JOBORDER (1)`
  for the general payroll; night differential (`2`) and special (`3`) never enter this flow.

**Step constants.** `payroll_processes.current_step` is an untyped integer today. Introduce
`App\Enums\PayrollStep`:

```php
const IMPORT = 1;         // file import + discrepancy review
const DEDUCTIONS = 2;     // per-employee deduction management
const RECEIVABLES = 3;    // per-employee receivable management
const ADJUSTMENTS = 4;    // low-net-pay adjustments
const SELECTION = 5;      // final employee list
const RECOMPUTE = 6;      // server-side generation
const PREVIEW = 7;        // review, then post/lock
```

**Step gate.** Add `App\Http\Middleware\PayrollStep` (parameterised: `payroll.step:2`). It
resolves the process for the request's `payroll_period_id` + `payroll_type` and rejects the
call when `current_step < required`. Forward movement is server-driven — the client stops
being able to `PUT /payroll-process/{id}` to any step it likes (see §9.9).

**Dirty flag.** Add `payroll_processes.is_dirty` (boolean) + `recomputed_at`. Any write in
steps 1–5 sets `is_dirty = true`; step 6 clears it. Step 7 refuses to post while dirty. This
is the mechanism that stops a preview from silently reflecting pre-adjustment numbers.

---

## Step 1 — Import (deductions + receivables) with discrepancy check

Today `EmployeeDeductionController::import()` parses the sheet and writes straight into
`employee_deductions`, skipping unknown employees with a `Log::warning` the user never sees.
That is the opposite of what this step needs. Replace it with a **stage → validate → review →
commit** flow.

### 1.1 Staging tables

```php
// import_batches
id
payroll_period_id      // FK
payroll_type           // int, PayrollType
kind                   // 'deduction' | 'receivable'
reference_code         // deductions.code / receivables.code read from the sheet
reference_id           // resolved deduction_id / receivable_id, nullable
file_name, file_path, file_size, file_type
sheet_month, sheet_year        // as declared inside the file
total_rows, valid_rows, blocking_rows, warning_rows
status                 // 'staged' | 'validated' | 'committed' | 'discarded'
uploaded_by, validated_at, committed_at
timestamps, softDeletes

// import_batch_rows
id
import_batch_id        // FK, cascade
row_number             // 1-based line in the sheet, for the review UI
employee_number        // raw, as read
employee_id            // resolved, nullable
raw                    // json — the whole row as parsed
amount                 // decimal(11,2), nullable when unparseable
resolution             // 'insert' | 'update' | 'skip'
severity               // 'ok' | 'warning' | 'blocking'
findings               // json array of {code, message, current, incoming}
timestamps
unique (import_batch_id, row_number)
index (import_batch_id, severity)
```

Nothing touches `employee_deductions` / `employee_receivables` until commit.

### 1.2 Discrepancy rules

Derived from `EmployeeDeduction`'s columns and constraints, from
`DeductionCarryForward::scopeInheritable()`, and from the unique index
`employee_deduction_unique (employee_id, deduction_id, payroll_period_id)`.

**Batch-level, blocking — abort before any row is read:**

| Code | Condition |
|---|---|
| `PERIOD_NOT_FOUND` | `payroll_period_id` does not resolve |
| `PERIOD_LOCKED` | `payroll_periods.locked_at` is not null |
| `PROCESS_PAST_IMPORT` | `current_step > 1` and the run is not explicitly reopened |
| `CODE_NOT_FOUND` | sheet's code has no `deductions`/`receivables` row |
| `CODE_INACTIVE` | reference row `status` is inactive |
| `CODE_NOT_EFFECTIVE` | period's date range falls outside the reference's effective range |
| `PERIOD_MISMATCH` | sheet's month/year ≠ period's month/year — **currently read and then ignored (§9.8)** |
| `TYPE_MISMATCH` | sheet targets Regular but the period's `employment_type` is Job Order, or vice-versa |
| `EMPTY_FILE` | zero data rows after the 2-row header |

**Row-level, blocking:**

| Code | Condition |
|---|---|
| `EMPLOYEE_NOT_FOUND` | `employee_number` matches no `employees` row (today: silently skipped) |
| `EMPLOYEE_NOT_IN_PERIOD` | no `employee_time_records` for `(employee_id, payroll_period_id)` — the employee is not part of this run |
| `DUPLICATE_IN_FILE` | the same `employee_number` appears twice for the same code; the unique index means the later row silently overwrites the earlier one today |
| `AMOUNT_INVALID` | blank, non-numeric, or negative |
| `AMOUNT_ZERO` | zero — treat as blocking, and require an explicit stop/delete instead |

**Row-level, warning — commits only after an explicit acknowledgement:**

| Code | Condition |
|---|---|
| `EMPLOYEE_EXCLUDED` | an `excluded_employees` row exists for the period with `is_removed = 0` |
| `EMPLOYEE_RESIGNED` | `employees.is_resigned` |
| `TARGET_STOPPED` | existing row for the period has `status = 'stopped'` — importing would resurrect it |
| `TARGET_COMPLETED` | existing row has `status = 'completed'`, or `total_paid >= total_term` |
| `TERMS_WOULD_RESET` | existing row has `with_terms = 1` and the sheet carries no term data — **the current importer hardcodes `with_terms => 0` on update and wipes it (§9.10)** |
| `AMOUNT_DIFFERS_FROM_DEFAULT` | amount ≠ `deductions.fixed_amount` / rule-derived amount; this is what `is_default` records |
| `AMOUNT_CHANGED` | amount differs from the same employee+code in the previous period (via `PayrollPeriodResolver::previousPeriod()`) by more than a configurable tolerance |
| `EXCEEDS_NET_PAY` | this amount plus already-recorded deductions would drive projected net pay below zero |
| `BELOW_THRESHOLD_AFTER` | projected net pay drops below `payroll.net_pay_exclusion_threshold` (5 000) — a preview of who will land in step 4 |

Every finding records `current` and `incoming` so the review screen can show the delta rather
than just a label.

### 1.3 Endpoints

```
POST   /payroll-imports                  stage a file        -> import_batches row + parsed rows
GET    /payroll-imports/{id}             batch header + counts by severity
GET    /payroll-imports/{id}/rows        paginated, filterable by severity
PUT    /payroll-imports/{id}/rows/{row}  correct a single row in staging, re-validate it
POST   /payroll-imports/{id}/revalidate  re-run validation (master data may have changed)
POST   /payroll-imports/{id}/commit      commit; body carries acknowledged warning codes
DELETE /payroll-imports/{id}             discard
```

Note that **no import route is registered today** — `import()` exists on the controller but
`routes/api.php` never points at it (§9.1), and `EmployeeReceivableController` has no import
method at all (§9.2).

### 1.4 Commit semantics

- Wrapped in one `DB::transaction`, chunked upsert on the existing unique key.
- Blocking rows are never written; the commit either writes every non-blocking row or none.
- On update, **only** the fields the sheet actually carries are written — never blanket
  `with_terms`/`total_term`.
- Writes an `EmployeeDeductionLog` / `EmployeeReceivableLog` entry per affected row with
  `action = 'import'` and the batch id, so a wrong import is traceable and reversible.
- Sets `import_batches.status = 'committed'`, marks the process dirty, and advances
  `current_step` to 2 the first time a batch commits.

### 1.5 Services

`ImportStagingService` (parse + persist staging), `ImportDiscrepancyService` (the rule table
above, one method per rule, pure and unit-testable), `ImportCommitService` (transactional
write). `ImportEmployeeDeduction` / `ImportEmployeeReceivable` shrink to pure row parsers with
no database writes.

---

## Step 2 — Employee Deductions (per employee)

Largely exists: `EmployeeDeductionController` (`store` single + bulk, `update` with
`toUpdate`/`toStop`/`toComplete`, `destroy`) over `EmployeeDeductionService`.

To add:

1. **`index` scoped to the run.** `GET /employee-deductions?payroll_period_id=&employee_id=`
   returning the employee's rows grouped by `deduction_group_id`
   (`Employee::groupedDeductions()` already does the roll-up).
2. **Carry-forward on entry.** On first arrival at step 2, run
   `DeductionCarryForward::carry($period)` once so the period is populated from the previous
   one. It is idempotent, so re-entering the step is safe. This replaces the preview's
   read-time fallback for this screen.
3. **Resume.** `toStop` exists; a matching `toResume` writing a `StoppageLog` close-out is
   missing.
4. **Trail on every mutation.** `EmployeeDeductionTrail` + `EmployeeDeductionLog` written by
   the service (or the existing `EmployeeDeductionObserver`), not by the controller.
5. **Dirty flag** set on every write.
6. **Step gate** `payroll.step:2`.

Terms are still **not** advanced here. `DeductionCarryForward::advanceTerms()` runs only at
posting — that separation is deliberate and already documented in the class.

---

## Step 3 — Employee Receivables (per employee)

Mirror of step 2 against `EmployeeReceivableController` / `EmployeeReceivableService` /
`EmployeeReceivableTrail`. Same additions: scoped `index`, resume, trail-on-mutation, dirty
flag, `payroll.step:3` gate.

One asymmetry to resolve: there is **no `ReceivableCarryForward`**. Decide explicitly whether
recurring receivables carry forward. If they do, generalise `DeductionCarryForward` (the
inheritable predicate is identical: not stopped/completed, terms remaining, `date_to` not
past) rather than writing a second copy — the class docblock records what happened last time
that rule existed in three places.

Computed receivables (PERA via `ComputationService::peraAmount()`, hazard via
`hazardAmount()`) are **not** managed here. They are derived in step 6 from the time record,
and manual entry of them should be rejected.

---

## Step 4 — Adjustments (low net pay)

**List:** employees in the run whose projected **net pay < 5 000**. The threshold already
exists as `payroll.net_pay_exclusion_threshold` (`config/payroll.php`), read via
`PayrollCodes::netPayExclusionThreshold()`, and `EmployeePreviewService::calculateAndClassify()`
already applies it.

**Reuse, don't duplicate.** Extract the projection loop from `EmployeePreviewService` into a
shared `NetPayProjector` returning `{basic, receivables, deductions, gross, net, first_half,
second_half}` per employee. Step 4 and step 7 then read the same numbers by construction.

One correction needed: `calculateAndClassify()` currently merges *manually excluded* and
*below-threshold* employees into one `excluded` bucket, and `exclusionReason()` falls back to
`'Salary Below Threshold'` for both. Step 4 wants **only** the below-threshold set, so the
projector must return the reason as a discriminated value (`below_threshold` |
`manually_excluded`) rather than a fallback string.

**Endpoints:**

```
GET  /payroll-adjustments?payroll_period_id=&payroll_type=
     -> employees under threshold, each with net, shortfall, and their
        deduction + receivable rows for the period

POST /employee-adjustments        (exists)
GET  /employee-adjustments/{id}   (exists)
```

**The gap:** `EmployeeAdjustmentService::create()` inserts an `employee_adjustments` row and
stops. It never touches the underlying `employee_deductions.amount` /
`employee_receivables.amount`, so the adjustment has **no effect on net pay**. The adjustment
must, in one transaction:

1. Insert the `EmployeeAdjustment` row (`amount`, `amount_to_pay`, `amount_balance`, `reason`,
   `action_by` — the columns already exist).
2. Update the target row's amount for **this period only** to `amount_to_pay`.
3. Carry `amount_balance` forward — either onto the next period's row, or by extending
   `total_term`. Pick one and state it; the balance column implies the former.
4. Write the trail (`EmployeeDeductionTrail.is_adjustment` already exists for exactly this).
5. Mark the process dirty.

Also add `DELETE /employee-adjustments/{id}` to reverse one — the interface has `delete()` but
the route is `only(['store','show'])`.

An employee still below threshold after adjustment is a legitimate outcome; step 5 decides
whether they go in the payroll or onto the exclusion list.

---

## Step 5 — Selection of employees

**The gap:** selection is not persisted anywhere. `EmployeePreviewController` accepts
`selected_employees[]` as a request array and passes it straight through — the choice lives in
the client and is lost on refresh.

New table:

```php
// payroll_selections
id
payroll_period_id     // FK
payroll_type          // int
employee_id           // FK
is_selected           // boolean
reason                // nullable — why forced in, or forced out
selected_by           // employee_profile_id of the actor
timestamps
unique (payroll_period_id, payroll_type, employee_id)
```

Deliberately **not** reusing `excluded_employees`: that table records a system/HR exclusion
(resigned, data issue) and is an input to the projection, whereas this records the human's
final include/exclude decision for one run. Conflating them makes "excluded because resigned"
indistinguishable from "excluded by the payroll officer this cycle".

**Seeding.** On first entry to step 5, insert one row per employee in the run, defaulting
`is_selected` to the step-4 classification: `true` for at or above threshold with no active
exclusion, `false` otherwise. The officer then overrides individually or in bulk.

**Endpoints:**

```
GET  /payroll-selections?payroll_period_id=&payroll_type=&status=selected|unselected|all
PUT  /payroll-selections            bulk toggle: [{employee_id, is_selected, reason}]
POST /payroll-selections/reset      re-seed from the current projection
```

**Exit criterion:** at least one selected employee; every unselected employee has a reason.

---

## Step 6 — Recompute / generate

**The gap, and the most important change in this plan:**
`EmployeePayrollController::store()` takes `employee_payroll` — basic pay, gross, deductions,
net, halves — **from the request body** and upserts it. The client is computing the payroll.
Any client bug, or anyone with the token, writes arbitrary amounts into `employee_payrolls`.
Step 6 must be a server-side generation that ignores client-supplied amounts entirely.

New `PayrollGenerationService`, invoked by
`POST /payroll-process/{id}/generate` (or `POST /employee-payrolls/generate`):

```
1. Guard      period not locked (fix GuardService first — §9.4);
              process at step >= 6; selection non-empty.
2. Resolve    employees = payroll_selections where is_selected = true,
              joined to employee_time_records for the period.
3. Per employee, chunked (500):
   a. basic_pay        <- employee_computed_salaries.basic_pay for the period
   b. computed receivables
        PERA   ComputationService::peraAmount(PayrollCodes::pera(), present_days,
                                              employment_type, absences,
                                              PayrollCodes::requiredDutyDays())
        Hazard ComputationService::hazardAmount(employment_type, salary_grade,
                                                basic_salary, absent_days, leave_days)
        Both are pure and DB-free — load the reference rows once, outside the loop.
   c. manual receivables  <- employee_receivables for the period
   d. deductions          <- employee_deductions for the period, post-adjustment
   e. gross = basic + receivables ; net = gross - deductions
   f. first_half  = period_type first_half  ? floor(net / 2)
                                            : locked first_half from the previous period
      second_half = net - first_half
   g. NIGHT DIFFERENTIAL IS NOT APPLIED HERE — separate payroll_type, separate run.
4. Upsert     employee_payrolls on (employee_id, payroll_period_id).
5. Delete     employee_payrolls rows for employees no longer selected.
6. Summary    PayrollSummaryService::updateOrCreate()
7. Bookkeep   payroll_periods.last_generated_at = now();
              process.is_dirty = false; recomputed_at = now(); current_step = 7
8. Event      PayrollGenerated
```

Whole thing in one transaction, idempotent — running it twice produces identical rows.
Regenerating after a step-2..5 edit is the normal path, not an exception.

`EmployeePayrollController::store()` keeps its route only if something still needs a manual
single-row write; otherwise retire it. Either way it must recompute server-side rather than
trust the payload.

`PayrollSummaryService::updateOrCreate()` also reads `$summary->total_night_differential`,
which its own `selectRaw` never selects — remove it here, or select it, but the general
payroll should be leaving it at zero.

---

## Step 7 — Preview

**Read from `employee_payrolls`**, the rows step 6 wrote. Do not re-derive on the fly: a
preview that recomputes can disagree with what will be posted, which is precisely the class of
bug the `EmployeePreviewService` docblock describes.

```
GET  /payroll-preview?payroll_period_id=&payroll_type=&type=all|included|excluded&page=
GET  /payroll-summary?payroll_period_id=
POST /payroll-reports          existing export (ExportPayrollService)
POST /payroll-process/{id}/post
```

Preview returns per-employee lines plus the `payroll_summaries` totals, and refuses to render
a "final" view while `is_dirty` — it reports "recompute required" instead.

**Posting**, one transaction:

1. `DeductionCarryForward::advanceTerms($period)` — the **only** place terms advance.
2. Mark deductions reaching `total_paid = total_term` as completed with `completed_at`.
3. `payroll_periods.posted_at = now()`, `locked_at = now()`, `status = POSTED`.
4. `payroll_processes.status = PayrollProcessStatus::COMPLETE`.
5. `DeductionCarryForward::carry($nextPeriod)` if the next period exists.
6. Write `LogsAndTrail`.

After posting, every step-1..6 endpoint for that period rejects on the lock.

`EmployeePreviewService` keeps its current pre-generation role (steps 4–5) but delegates the
arithmetic to the shared `NetPayProjector`; step 7 no longer routes through it.

---

## 8. Build order

| Phase | Work | Why first |
|---|---|---|
| **0 — done** | Fixed §9.1, 9.2, 9.3, 9.4, 9.7, 9.8, 9.10, 9.13; §9.9 enforced server-side. Added `PayrollStep`, `is_dirty`/`recomputed_at`, `PayrollStepGate`. §9.5 partially addressed — see the note below. | Everything else assumes a correct lock and a real step machine |
| 1 | Extract `NetPayProjector` from `EmployeePreviewService`; add the reason discriminator. | Steps 4, 5 and 7 all depend on one projection |
| 2 | Step 6 `PayrollGenerationService` + step 7 read-from-`employee_payrolls`. | Closes the client-computes-payroll hole; makes 1–5 verifiable end-to-end |
| 3 | Step 5 `payroll_selections`. | Step 6 needs a persisted input set |
| 4 | Step 4 adjustments that actually mutate amounts + reverse. | |
| 5 | Steps 2–3: scoped index, resume, trails, carry-forward decision for receivables. | |
| 6 | Step 1 staging + discrepancy engine + review endpoints. | Largest piece, and the only one whose absence has a manual workaround |

**Template layout.** The real sample files turned out not to match what the importer assumed:
data starts on row 4 (the importer sliced from row 3, the column-header row), and the
month/year row is present only in the CSV exports — every .xlsx template leaves it blank. The
Term/Months Paid columns are real and were being ignored, and a zero amount is normal rather
than an error. `docs/IMPORT_TEMPLATE.md` is the specification, the zero/terms/duplicate rules,
and the B1 code cleanup list; `payroll:audit-import-files` and `payroll:import-template` are
the tools for the cleanup.

**What Phase 0 landed.** `App\Enums\PayrollStep` (the seven constants plus
`canAdvance()`), `PayrollStepGate` middleware registered as `payroll.step` and applied to the
two new import routes, `payroll_processes.is_dirty`/`recomputed_at` with
`markDirty()`/`markRecomputed()`/`isDirty()` on `PayrollProcessService`, a period-scoped
`GuardService`, `PayrollLockedException` (403) and `InvalidPayrollStepException` (422), and a
working import on both the deduction and receivable side that reports its skipped rows. 39 new
tests across `PayrollStepTest`, `PayrollGuardScopeTest`, `PayrollImportTest`,
`PayrollProcessStepTest` and `ImportTemplateTest`; suite at 119 passing.

**Still open from Phase 0's row.** §9.5 — `EmployeePayrollController::store()` still takes the
computed amounts from the request body. Phase 0 only made its lock check name the right
period; the payload is still trusted. That endpoint is replaced wholesale by
`PayrollGenerationService` in Phase 2, so it is fixed there rather than patched twice. Steps
2–5 do not yet call `markDirty()` — those call sites land with each step's own phase, and
`isDirty()` reads true for any run that has not been recomputed, so nothing can read as clean
before then. `PayrollStepGate` is likewise attached only to the import routes so far;
the later steps' gates land with the endpoints they guard, because the existing
deduction/receivable routes do not carry `payroll_type` and gating them now would break the
client.

Each phase lands with tests alongside the existing golden-master suite
(`bdfa37f test: add payroll golden master, fixtures and unit coverage`) — the projector and
the discrepancy rules are pure functions and should be unit-tested directly.

---

## 9. Defects and gaps found in the current code

1. **[FIXED — Phase 0] Import is unreachable.** `EmployeeDeductionController::import()` exists; `routes/api.php`
   registers no route for it (`apiResource` covers only the seven standard verbs).
2. **[FIXED — Phase 0] No receivable import.** `ImportEmployeeReceivable` exists as an import class, but
   `EmployeeReceivableController` has no `import()` method.
3. **[FIXED — Phase 0] `ImportFiles` model does not match its table.** Fillable declares `receivables_id`,
   `file_size`, `file_type`, `imported_at`; the migration creates `receivable_id` and `path`
   and none of the other three. Any write through this model fails or silently drops fields.
4. **[FIXED — Phase 0] `GuardService::ensureNotLocked()` guards the wrong row.** It reads
   `PayrollPeriod::where('is_active', true)->first()` — the *globally* active period, not the
   one being mutated. With Regular and Job Order runs open concurrently (the stated
   requirement), it can check the Job Order period's lock while writing Regular data, or vice
   versa. It must take the target `payroll_period_id`.
5. **[OPEN — Phase 2] The client computes the payroll.** `EmployeePayrollController::store()` upserts
   `basic_pay`, `gross_pay`, `total_deductions`, `net_pay`, `first_half`, `second_half`
   straight from the request body. See step 6.
6. **Employment type is inconsistent.** `App\Enums\EmploymentType` has only `regular` and
   `contractual` — no job order — while `ComputationService` branches on the string literals
   `'Job Order'` and `'Permanent Part-time'` coming from UMIS. A general payroll that must
   distinguish Regular from Job Order needs one canonical set of values.
7. **[FIXED — Phase 0] Unknown employees vanish.** `ImportEmployeeDeduction` logs a warning and `continue`s.
   Nobody operating the system sees it. This is the core motivation for step 1.
8. **[FIXED — Phase 0] Sheet month/year is read and ignored.** The importer parses `$monthName`/`$year` from the
   file, computes `$month`, and then never uses any of them — the period comes from the
   request. A February file imports cleanly into a March period.
9. **[FIXED — Phase 0] The step counter is client-controlled.** `PayrollProcessController::update()` writes
   whatever `current_step` and `status` the request supplies, with no validation that the step
   exists, that it advances by one, or that the previous step's exit criteria were met.
10. **[FIXED — Phase 0] Import updates wipe term data.** The importer's update payload hardcodes
    `'with_terms' => 0` and omits `total_term`/`total_paid`, so re-importing an amount for a
    term-based loan converts it to a non-term deduction and loses the amortisation schedule.
11. **Adjustments are inert.** `EmployeeAdjustmentService::create()` records the adjustment but
    never changes the deduction or receivable it refers to, so net pay is unaffected. See
    step 4.
12. **`PayrollSummaryService` reads a column it never selects.**
    `$summary->total_night_differential` is not in the `selectRaw` list, so it is always null
    and coalesces to 0.
13. **[FIXED — Phase 0] Stray debug.** `Log::info('TEST')` in `EmployeePreviewController::index()`.
14. `app/Services/ExcludeEmployeeService.php` and `ExcludedEmployeeService.php` both exist —
    likely one is dead.
