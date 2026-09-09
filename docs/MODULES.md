# ZCMC Payroll Server — Module Documentation

Laravel API backend for payroll processing. Architecture per module: **Controller** (HTTP layer, `app/Http/Controllers`) → **Service** (business logic, `app/Services`) → **Repository**, bound via `Contract\Repositories\*Interface` and wired in `RepositoryServiceProvider` (`app/Providers/RepositoryServiceProvider.php`) → **Eloquent Model** (`app/Models`). Request validation lives in `app/Http/Requests`, response shaping in `app/Http/Resources`, and DTOs passed between layers in `app/Data` (Spatie Laravel-Data style objects). Swagger/OpenAPI annotations for most endpoints live in `app/Http/Documentation`.

All routes are registered in `routes/api.php` and (aside from sign-in/sign-out/check-connection) sit behind the `auth.token` middleware (`App\Http\Middleware\AuthenticateToken`), which checks a `personal_access_tokens` row rather than Laravel's default session/Sanctum guard.

---

## 1. Authentication

**Purpose:** Employee sign-in/sign-out and token issuance/validation. Distinct from Laravel's stock `users` table — auth is keyed on UMIS employee identity plus a PIN.

- **Controllers:** `Authentication/LoginController` (`sign-in`, `sign-out`, `check-connection`), `Authentication/AuthenticationController` (`authentications` — introspect current auth)
- **Model:** `PersonalAccessToken` (table `personal_access_tokens`: `employee_id`, `email`, `name`, `authorization_pin`, `token`, `expire_at`, `permissions`, `last_used_at`)
- **Middleware:** `AuthenticateToken` (validates the bearer token against `personal_access_tokens`), `UMIS` middleware for UMIS-scoped calls
- **Supporting:** `LoginTrail` model/table records every auth-relevant action (`module_name`, `methods`, `description`, `status`, `action_by`)
- **Endpoints:**
  - `POST /sign-in`, `POST /sign-out`, `GET /check-connection` (public)
  - `GET /authentications` (authenticated)

---

## 2. Employee Management

**Purpose:** Local, payroll-relevant mirror of employee master data, sourced from UMIS and enriched with payroll-only flags/relations.

- **Controller:** `Employee/EmployeeController` (`index`, `show` only — employees aren't created/edited locally, only synced)
- **Model:** `Employee` (table `employees`) — uses `Spatie\Activitylog` (`LogsActivity`) so every fillable change is recorded to `activity_log`
- **Key fields:** `employee_profile_id` (unique, soft link to UMIS), `employee_number`, name parts (+ computed `full_name` accessor), `designation`, `assigned_area` (JSON), `status`, `is_newly_hired`, `is_excluded`, `is_resigned`
- **Relations:** one salary, one time record, one computed salary, many deductions, many receivables, many exclusion records, many payroll runs (see ERD)
- **Business logic:** `Employee::groupedDeductions()` / `getGroupedDeductionsAttribute()` roll up an employee's active deductions by `deduction_group_id` for display
- **Service:** `EmployeeService`
- **Endpoints:** `GET /employees`, `GET /employees/{id}`

### 2a. Employee Sync (UMIS ingestion)

- **Controllers:** `UMIS/FetchEmployeeController` (`index`, `store` — triggers/reads a fetch job), `UMIS/FetchingProgressController` (`index` — poll job progress)
- **Services:** `EmployeeSyncService` (upserts UMIS employee profiles into `employees`), `FetchEmployeeMapperService` (maps UMIS DTO shape → local `EmployeeData`)
- **Mechanism:** Employees are fetched from UMIS and staged through Redis before being upserted; `add_employee_sync_unique_indexes` migration added unique constraints on `employees.employee_profile_id` and `(employee_id, payroll_period_id)` on `excluded_employees` to make the upsert idempotent.
- **Endpoints:** `GET/POST /fetch-employees`, `GET /fetch-progress`

### 2b. Excluded Employees

**Purpose:** Per-period exclusion list — employees flagged out of a payroll run (e.g., resigned, on leave without pay beyond a threshold, data issues) with a reason and a removable flag.

- **Controller:** `Employee/ExcludedEmployeeController` (`index`, `store`, `update`, `destroy`)
- **Model:** `ExcludedEmployee` (table `excluded_employees`: `employee_id`, `payroll_period_id` nullable, `reason`, `is_removed`)
- **Service:** `ExcludedEmployeeService` / `ExcludeEmployeeService`
- **Endpoints:** `GET/POST/PUT/DELETE /excluded-employees`

### 2c. Employee Time Records

**Purpose:** Per-employee, per-period attendance/time totals (working minutes/hours, overtime, undertime, leave, absences, night duty hours) — the raw input to salary computation.

- **Controller:** `Employee/EmployeeTimeRecordController`
- **Model:** `EmployeeTimeRecord` (table `employee_time_records`, unique per `(employee_id, payroll_period_id)`)
- **Service:** `EmployeeTimeRecordService`
- **Relation:** feeds `EmployeeComputedSalary` (1:1) and `EmployeePayroll` (1:many)
- **Endpoints:** full `apiResource` at `/employee-time-records`

### 2d. Employee Preview

**Purpose:** Read-only, computed preview of an employee's payroll figures (likely pre-generation sanity check), assembled from time record + deductions + receivables without persisting a payroll row.

- **Controller:** `Employee/EmployeePreviewController` (`index`, `show`)
- **Service:** `EmployeePreviewService`
- **Resource:** `EmployeePreviewResource`
- **Endpoints:** `GET /employee-preview`, `GET /employee-preview/{id}`

### 2e. Employee Adjustments

**Purpose:** Manual correction entries against an employee's deduction or receivable within a period (amount vs. amount-to-pay vs. balance), with a free-text reason and actor.

- **Controller:** `Employee/EmployeeAdjustmentController` (`store`, `show` only)
- **Model:** `EmployeeAdjustment` (table `employee_adjustments`: `payroll_period_id`, `employee_deduction_id` nullable, `employee_receivable_id` nullable, `amount`, `amount_to_pay`, `amount_balance`, `reason`, `action_by`)
- **Service:** `EmployeeAdjustmentService`
- **Endpoints:** `POST /employee-adjustments`, `GET /employee-adjustments/{id}`

---

## 3. Deductions (Settings module)

**Purpose:** Catalog of deduction types (e.g. loans, contributions) and the salary-bracket rules used to price them, independent of any one employee.

- **Controllers:** `Settings/DeductionController`, `Settings/DeductionGroupController`, `Settings/DeductionRuleController`
- **Models:**
  - `DeductionGroup` (table `deduction_groups`: `name`, `code`, `deduction_group_uuid`) — groups deductions for reporting (e.g. "Government", "Loans")
  - `Deduction` (table `deductions`: belongs to a group; `type` fixed/percentage/conditional, `billing_cycle`, `percent_value`, `fixed_amount`, effective date range, `status`)
  - `DeductionRule` (table `deduction_rules`: belongs to a deduction; salary-bracket-based override — `min_salary`/`max_salary`, `apply_type`, `value`, effective date range)
- **Seeders:** `DeductionGroupSeeder`, `DeductionSeeder` (large — seeds the standard government/loan deduction catalog)
- **Services:** `DeductionService`, `DeductionGroupService`, `DeductionRuleService`
- **Endpoints:** full `apiResource` at `/deductions`, `/deduction-groups`, `/deduction-rules`

### 3a. Employee Deductions

**Purpose:** A deduction assigned to a specific employee, with amortization terms (`total_term`/`total_paid`), a billing cycle, and lifecycle dates (`effective_date`, `deduct_at`, `stopped_at`, `completed_at`).

- **Controller:** `Employee/EmployeeDeductionController`
- **Model:** `EmployeeDeduction` (table `employee_deductions`)
- **Service:** `EmployeeDeductionService`
- **Import:** `ImportEmployeeDeduction` (Laravel-Excel import class) + `import_files`/`import_file_logs` tables track bulk-upload provenance
- **Observer:** `EmployeeDeductionObserver` — likely fires on status transitions to write logs/trails
- **Child records:**
  - `EmployeeDeductionLog` (table `employee_deduction_logs`) — free-form action log (`action_by`, `action`, `remarks`, `details`)
  - `EmployeeDeductionTrail` (table `employee_deduction_trails`) — actual payment history per term (`amount_paid`, `balance`, `date_paid`, `is_last_payment`, `is_adjustment`)
  - `StoppageLog` (table `stoppage_logs`, shared with receivables) — records suspend/resume actions with a date range and reason
- **Console commands:** `SuspendDeductions`, `ResumeDeduction`, `CheckEmployeeDeductions`
- **Endpoints:** full `apiResource` at `/employee-deductions`; trail sub-resource at `/employee-deduction-trail` (`index`, `store`, `show`, `destroy`) via `Trail/EmployeeDeductionTrailController`

---

## 4. Receivables (Settings + Employee)

**Purpose:** Mirror of the Deductions module for amounts owed *to* the employee (reimbursements, allowances, etc.) rather than owed *by* them. Structurally near-identical, minus a grouping table.

- **Controller:** `Settings/ReceivableController`
- **Models:** `Receivable` (table `receivables`), `ReceivableRule` (table `receivable_rules`, salary-bracket pricing)
- **Seeder:** `ReceivableSeeder`
- **Service:** `ReceivableService`
- **Endpoints:** full `apiResource` at `/receivables`

### 4a. Employee Receivables

- **Controller:** `Employee/EmployeeReceivableController`
- **Model:** `EmployeeReceivable` (table `employee_receivables`, terms/lifecycle fields mirror `EmployeeDeduction`)
- **Service:** `EmployeeReceivableService`, `EmployeeReceivableTrailService`
- **Import:** `ImportEmployeeReceivable`
- **Observer:** `EmployeeReceivableObserver`
- **Child records:** `EmployeeReceivableLog` (table `employee_receivable_logs`), `EmployeeReceivableTrail` (table `employee_receivable_trails`), shares `StoppageLog` with deductions
- **Console commands:** `SuspendReceivables`, `ResumeReceivable`
- **Endpoints:** full `apiResource` at `/employee-receivables`

---

## 5. Night Differential

**Purpose:** Computes the pay premium for hours worked at night, per employment type and effective-dated rate.

- **Controller:** `NightDifferential/NightDifferentialRuleController` (`index`, `store`, `show`)
- **Models:**
  - `NightDifferentialRules` (table `night_differential_rules`: `employment_type`, `rate_percent`, `effective_date`, `is_active`)
  - `EmployeeNightDuties` (table `employee_night_duties`: per-employee, per-period night shift log — `duty_date`, `time_in`, `time_out`, `night_minutes`, `night_hours`)
  - `EmployeeNightDiffComputation` (table `employee_night_diff_computations`: finalized per-period totals — `total_night_hours`, `total_night_amount`, `hourly_rate`, `rate_percent`, `is_finalized`, `computed_at`)
- **Service:** `NightDifferentialRuleService`
- **Endpoints:** `GET/POST /night-differential-rules`, `GET /night-differential-rules/{id}`

---

## 6. Payroll Processing

**Purpose:** The core payroll run — periods, the stepped generation process, per-employee payroll rows, and the aggregate summary/report.

### 6a. Payroll Periods

- **Controller:** `Payroll/PayrollPeriodController` (`index`, `update`, plus `payroll-period-lists`)
- **Model:** `PayrollPeriod` (table `payroll_periods`, unique per `(month, year, employment_type, period_type)`)
- **Fields:** `payroll_type` (enum `PayrollType`), `period_type` (enum `PayrollPeriodType` — half-month/monthly), `period_start`/`period_end` (day-of-month cutoffs), `days_of_duty`, `status` (enum `PayrollStatus`), `is_active`, `posted_at`, `locked_at`, `last_generated_at`
- **Service:** `PayrollPeriodService`; **Support:** `PayrollPeriodResolver` (resolves the active/target period), `PayrollCodes` (status/type code constants)
- **Middleware:** `ActivePayrollPeriod` guards mutating endpoints against a locked/inactive period
- **Endpoints:** `GET /payroll-periods`, `PUT /payroll-periods/{id}`, `GET /payroll-period-lists`

### 6b. Payroll Process

**Purpose:** Tracks the multi-step generation workflow for a period (time records → computed salary → deductions/receivables → night differential → final payroll), one row per `(payroll_period_id, payroll_type)`.

- **Controller:** `Payroll/PayrollProcessController` (`store`, `show`, `update`)
- **Model:** `PayrollProcess` (table `payroll_processes`: `current_step`, `status` (enum `PayrollProcessStatus`), `started_by`, `started_at`)
- **Service:** `PayrollProcessService`; **Support:** `GenPayroll` helper, `GuardService::ensureNotLocked()` guards against re-running a locked period
- **Endpoints:** `POST /payroll-process`, `GET/PUT /payroll-process/{id}`

### 6c. Employee Payroll

**Purpose:** The generated, per-employee payroll line for a period — basic pay, receivables, deductions, gross/net, and half-month split.

- **Controller:** `Payroll/EmployeePayrollController` (`index`, `store`, `show`)
- **Model:** `EmployeePayroll` (table `employee_payrolls`, links `employee_id` + `employee_time_record_id` + `payroll_period_id`)
- **Service:** `EmployeePayrollService`, `ComputationService` (core pay computation), `DeductionCarryForward` (support class carrying unpaid deduction balances into the next period)
- **Event:** `PayrollGenerated`
- **Endpoints:** `GET/POST /employee-payrolls`, `GET /employee-payrolls/{id}`

### 6d. Payroll Summary & Reports

- **Controllers:** `Payroll/PayrollSummaryController` (`index`, `store`), `Payroll/PayrollReportController` (`index`, `store`)
- **Models:** `PayrollSummary` (table `payroll_summaries` — period totals: employees, deductions, receivables, gross, net, night differential)
- **Services:** `PayrollSummaryService`, `PayrollReportService` (delegates export to `ExportPayrollService`)
- **Export:** `ExportPayrollService` + `Exports/ExportEmployeePayroll` (Laravel-Excel export class) — generates downloadable payroll reports
- **Endpoints:** `GET/POST /payroll-summary`, `GET/POST /payroll-reports`

---

## 7. Trails & Audit

**Purpose:** Cross-cutting logging — who did what, when, and (for financial records) the running balance/state history.

- **`LogsAndTrail`** (table `logs_and_trails`) — generic action log: `module`, `action_type`, `reference_table`, `reference_id`, `changes` (JSON), `ip_address`, `status`
- **`TransactionLog`** (table `transaction_logs`) — API-call-level log: `module`, `action`, `status`, `ip_address`, `serverResponse`, `affected_entity` (JSON), plus the acting employee's identity — written via `TransactionLogService`, likely from a request-lifecycle hook
- **`LoginTrail`** (table `login_trails`) — auth-specific action log
- **`StoppageLog`** (table `stoppage_logs`) — suspend/resume history shared by deductions and receivables
- **`activity_log`** (Spatie Activitylog package table) — model-level change history; currently wired to `Employee` (`LogsActivity` trait, logs fillable/dirty attributes under log name `employee`)
- **Employee-Deduction-Trail controller:** `Trail/EmployeeDeductionTrailController` exposes deduction payment history as its own resource (see §3a)

---

## 8. External Integration: UMIS

**Purpose:** UMIS ("Unified [HR] Information System") is the hospital's separate HR/attendance/leave system. This payroll app treats it as an upstream source of truth for employee identity, org structure, schedules, and leave, reached via a second Eloquent database connection (`app/Models/UMIS/*`), not this app's own migrated schema.

- **Controllers:** `UMIS/FetchEmployeeController`, `UMIS/FetchingProgressController`
- **Middleware:** `Http/Middleware/UMIS.php`
- **Services:** `EmployeeSyncService` (sync orchestration), `FetchEmployeeMapperService` (DTO mapping), `UmisHttpRequestHelper` (Helpers)
- **Contract:** `PortalCacheReaderInterface` / `PortalCacheRepository` — suggests UMIS data is staged through a cache (Redis) layer before being read into this app
- **Models (read path only, no local migrations):** `EmployeeProfile`, `PersonalInformation`, `Department`, `Division`, `Section`, `Unit`, `Designation`, `AssignArea`, `Plantilla`, `SalaryGrade`, `EmploymentType`, `Schedule`, `TimeShift`, `Holiday`, `DailyTimeRecords`, `OfficialTime`, `OfficialBusiness`, `CTOApplication`, `LeaveType`, `LeaveApplication`, `LeaveApplicationLog`, `LeaveApplicationRequirement`, `EmployeeLeaveCredit`, `InActiveEmployee`
- **Data flow:** UMIS employee/attendance/leave data → `FetchEmployeeController` triggers a fetch/sync job → results staged (Redis) → `EmployeeSyncService` upserts into local `employees` (and presumably feeds `EmployeeTimeRecord` generation) → rest of the payroll pipeline runs against local tables only
- **Console command:** `FetchEmployeeTimeRecord` — scheduled/manual pull of UMIS time records

---

## Cross-module architecture notes

- **Repository pattern:** every domain model has a `Contract\<Model>Interface` + `Contract\Repositories\<Model>Repository` bound to a concrete implementation in `RepositoryServiceProvider`; services depend on the interface, not the Eloquent model directly — swap implementations without touching controllers.
- **Data objects:** `app/Data/*Data.php` (Spatie Laravel-Data) are the typed payloads passed from Requests into Services, and often from Services back out as return types — check here before assuming raw arrays flow through the service layer.
- **Enums:** `app/Enums` (`BillingCycle`, `EmployeeStatus`, `EmploymentType`, `PayrollPeriodType`, `PayrollProcessStatus`, `PayrollStatus`, `PayrollType`) back most of the `string`/`integer` status columns above — check there for the valid value set of any `status`/`type` column before writing queries against it.
- **Locking:** `PayrollPeriod.locked_at` / `EmployeeTimeRecord.locked_at` plus `GuardService::ensureNotLocked()` and the `ActivePayrollPeriod` middleware are the mechanism preventing edits to a period that's already been posted/generated.
