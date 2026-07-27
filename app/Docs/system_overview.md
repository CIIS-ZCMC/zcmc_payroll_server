# ZCMC Payroll System — Overview

Backend for the **Zamboanga City Medical Center** payroll system. It computes employee pay per payroll period from time records, applies deductions and receivables (including installment-based ones), computes night-differential pay, and produces versioned, auditable payroll runs. It exposes a REST API for the front end and a Filament admin panel for master-data management.

> Companion docs in this folder: [`payroll_migration_runbook.md`](payroll_migration_runbook.md) (schema/migration reference) and [`models_resources_requests_summary.md`](models_resources_requests_summary.md) (models, resources, requests catalog).

---

## 1. Tech Stack

| Layer | Technology |
|---|---|
| Language | PHP 8.2 |
| Framework | Laravel 12 |
| Admin UI | Filament 5 (Livewire-based) |
| Audit | spatie/laravel-activitylog 4 |
| Database | MySQL 8 (`new_zcmc_payroll_db`) |
| Code style | Laravel Pint |
| Tests | Pest 3 / PHPUnit 11 |

**At a glance:** 30 domain tables · 30 domain models · 20 repositories + 20 interfaces · 10 services · 14 API controllers · 68 API routes · 25 form requests · 14 API resources · 9 Filament admin resources.

---

## 2. Architecture

The app follows a **layered, contract-driven architecture**. Every request flows through the same path, and each layer has one responsibility:

```
HTTP Request
   │
   ▼
Route (routes/api.php, /api/v1/*)
   │
   ▼
Form Request  ──────────────►  validation (app/Http/Requests)
   │
   ▼
Controller (app/Http/Controllers/Api)  ── thin; no business logic
   │
   ▼
Service (app/Services)  ── business logic, transactions, orchestration
   │
   ▼
Interface (app/Contract)  ── contract, bound in AppServiceProvider
   │
   ▼
Repository (app/Repositories)  ── all Eloquent/DB access
   │
   ▼
Model (app/Models)  ── schema, relationships, casts, activity logging
   │
   ▼
API Resource (app/Http/Resources)  ── shapes the JSON response
```

**Key principles:**

- **Controllers are thin.** They validate (via Form Request), delegate to a service, and wrap the result in an API Resource. No queries, no business rules.
- **Services own the business logic.** Multi-step operations, `DB::transaction()` boundaries, idempotency checks, and cross-repository orchestration live here.
- **Repositories own data access.** Every Eloquent query lives behind an interface. Nothing else in the app queries models directly, which keeps queries testable and swappable.
- **Interfaces are bound to repositories** in `App\Providers\AppServiceProvider::$bindings_map` (20 bindings), so services depend on abstractions, not concretions.

### Dependency binding

`AppServiceProvider` maps each `*Interface` → `*Repository`:

```php
private array $bindings_map = [
    EmployeeInterface::class      => EmployeeRepository::class,
    PayrollRunInterface::class    => PayrollRunRepository::class,
    // …20 total
];
```

---

## 3. Domain Model

The 30 domain tables, grouped by area. Foreign keys are ordered so every referenced table is created first (see the migration runbook).

### Reference / setup data
| Table | Purpose |
|---|---|
| `deduction_groups`, `receivable_groups` | Categories for deduction/receivable types |
| `deductions`, `receivables` | Catalog of deduction/receivable types |
| `late_deduction_matrix` | Bracketed late penalties (minutes range → amount, per employment type) |
| `night_differential_rules` | ND rules per employment type (time window, rate type, effectivity) |

### Employees
| Table | Purpose |
|---|---|
| `employees` | Employee master data (the hub — most tables FK to it) |
| `employee_salaries` | Salary snapshot **per payroll period** (base salary, grade, step) |
| `employee_exclusions` | Excludes an employee from a specific period's payroll |

### Payroll cycle
| Table | Purpose |
|---|---|
| `payroll_periods` | The cycle: employment type + month + year + payroll type; lifecycle status; `is_active` flag |
| `payroll_processes` | Step-tracked workflow with pessimistic locking (`lock_expires_at`) |
| `employee_time_records` | Attendance aggregates per employee per period (hours, OT, UT, absences, ND) |
| `payroll_runs` | **Versioned** execution of payroll (self-referencing `is_reversed_from`) |
| `employee_computed_salaries` | Derived pay rates per employee per run (4-decimal precision) |

### Deductions & Receivables (mirrored 4-table subsystems)
| Pattern | Deduction side | Receivable side |
|---|---|---|
| Assignment | `employee_deductions` | `employee_receivables` |
| Installment terms | `employee_deduction_terms` | `employee_receivable_terms` |
| Per-run payment | `employee_deduction_payments` | `employee_receivable_payments` |
| Audit trail | `employee_deduction_logs` | `employee_receivable_logs` |

### Payroll output
| Table | Purpose |
|---|---|
| `employee_payrolls` | The payslip row (gross → deductions → net; first-half / second-half split) |
| `employee_payroll_details` | Line-item breakdown (absent/UT/late deductions, OT, ND pay) |
| `payroll_adjustments` | Post-run corrections (bonus/penalty/correction/reversal) with approval workflow |
| `payroll_summaries` | One aggregate row per run (management report) |

### Night differential (its own sub-pipeline)
`employee_night_duties` (raw duty logs) → `night_differential_runs` (versioned batch) → `employee_night_differentials` (computed amount per duty).

### Audit
| Table | Purpose |
|---|---|
| `logs_and_trails` | Domain-level audit log (module, action, changes JSON, IP) |
| `activity_log` | Automatic model-change audit (spatie/laravel-activitylog) |

---

## 4. Payroll Processing Flow

```
Setup: catalogs (deductions/receivables), ND rules, late matrix
   │
Period created  (per employment type + month + payroll type; one active at a time)
   │
Salaries assigned + time records imported  ──► employees excluded if needed
   │
ND duties logged  ──►  ND run computes differential amounts
   │
Payroll RUN (version N)
   ├── computed salaries (rates per employee)
   ├── deduction/receivable payments (advance one installment term each)
   ├── employee payrolls + details (gross → deductions → net; half splits)
   ├── adjustments (approved corrections)
   └── summary (run-level totals)
   │
Post → Lock → Release      (or Reverse → new version N+1)
```

**Design decisions visible in the schema:**

- **Versioning over mutation** — payroll and ND runs are never edited in place. Reversals create a new version linked via `is_reversed_from`.
- **Period-scoped snapshots** — salaries and time records are frozen per period, so recomputing an old period uses the data as it was.
- **Idempotency guards** — 15 unique constraints (employee+period, deduction+run, duty+run…) make double-processing structurally impossible.
- **Precision split** — rates use `decimal(15,4)`, money uses `decimal(15,2)`.
- **Locking at three levels** — period, process (with expiry), and individual payroll rows — supporting a finalize-and-freeze workflow.

---

## 5. API Layer

- **68 routes** under the `/api/v1` prefix, defined in `routes/api.php` and registered in `bootstrap/app.php`.
- Standard CRUD via `Route::apiResource`, trimmed per entity (`except`/`only`) to match the service capabilities.
- **Custom domain actions** beyond CRUD, e.g.:
  - Periods: `activate`, `lock`, `active` (get the active period)
  - Runs: `complete`, `lock`, `reverse`
  - Deductions/receivables: `stop`
  - Time records: `include`, `exclude`
  - Salaries: bulk `import`
- Responses are shaped by **API Resources** using `whenLoaded()` to avoid N+1 queries.
- Validation is centralized in **25 Form Requests** (`Store*`/`Update*`), using `Rule::in()` for enums and `exists:` for foreign keys.

> **Note:** the API currently has **no authentication**. Adding Laravel Sanctum token auth to the `v1` route group is the recommended next step (it also enables causer attribution in the activity log — see §7).

### Getting the active payroll period

Go through the service/repository layer — do not query the model directly:

```php
$activeId = app(PayrollPeriodService::class)->getActive()?->id;   // or GET /api/v1/payroll-periods/active
```

---

## 6. Admin Panel (Filament 5)

- Panel at **`/admin`**, branded **"ZCMC Payroll"**, primary color `rgb(10, 62, 48)` (dark green) with colorful status accents.
- Navigation is grouped into: **Employee Management · Payroll Setup · Deductions · Receivables · System**.
- **9 admin resources**: Employees, Payroll Periods, Night Differential Rules, Late Deduction Matrix, Deduction Groups, Deductions, Receivable Groups, Receivables, and **Activity Log** (read-only). Master-data models are admin-editable; transactional data (runs, payslips, payments) is intentionally API-only.
- Default dev admin: `admin@zcmc.local` / `password` — **change before production**.

---

## 7. Audit & Activity Logging

Two complementary systems:

1. **Domain logs** (`logs_and_trails`, `employee_deduction_logs`, `employee_receivable_logs`) — intentional business events written by the services (e.g. "assigned", "stopped", "completed").
2. **Automatic model-change audit** (spatie/laravel-activitylog → `activity_log`) — every create/update/delete on 14 core models, capturing only the changed fillable attributes.

**Activity-log setup:**
- Shared trait `App\Models\Concerns\LogsModelActivity` configures logging once (`logFillable` + `logOnlyDirty` + `dontSubmitEmptyLogs`, grouped by table name).
- Custom model `App\Models\Activity` (set as `activity_model` in config) carries an **observer** (`App\Observers\ActivityObserver`) that enriches every entry with the request **IP** and **user agent** — context spatie doesn't capture by default.
- Browsable in the admin panel under **System → Activity Log** (read-only, color-coded events, filterable by module/event).

> Causer attribution works for authenticated Filament actions; API-driven changes log as "System / API" until Sanctum auth is added.

---

## 8. Directory Map

```
app/
├── Contract/            # 20 repository interfaces
├── Repositories/        # 20 Eloquent repositories (all DB access)
├── Services/            # 10 business-logic services
│   ├── Fetch/           #   caching helper
│   └── Helper/          #   computation helpers
├── Models/              # 30 domain models + Activity + User
│   └── Concerns/        #   LogsModelActivity trait
├── Observers/           # ActivityObserver
├── Http/
│   ├── Controllers/Api/ # 14 thin API controllers
│   ├── Requests/        # 25 form requests (validation)
│   └── Resources/       # 14 API resources (JSON shaping)
├── Filament/Resources/  # 9 admin resources
└── Providers/
    ├── AppServiceProvider.php        # interface→repository bindings
    └── Filament/AdminPanelProvider.php

routes/api.php           # 68 API routes (/api/v1)
config/activitylog.php   # points to App\Models\Activity
database/migrations/     # 37 migrations
app/Docs/                # this documentation
```

---

## 9. Getting Started

```bash
cd server

# 1. Environment (note: APP_NAME must be quoted in .env)
cp .env.example .env         # if not already present
php artisan key:generate

# 2. Requirements: PHP 8.2 with the `intl` extension enabled (Filament needs it)

# 3. Database (MySQL 8, database new_zcmc_payroll_db)
php artisan migrate

# 4. Create an admin user for the panel
php artisan make:filament-user

# 5. Run
php artisan serve            # http://localhost:8000
#   API:   http://localhost:8000/api/v1/...
#   Admin: http://localhost:8000/admin
```

---

## 10. Open Items / Next Steps

- **API authentication** — add Laravel Sanctum to the `/api/v1` group (also enables activity-log causer attribution).
- **Actor foreign keys** — `started_by_id`, `generated_by_id`, `created_by_id`, etc. are not FK-constrained; confirm they reference a users table and add constraints.
- **Active-period scoping** — `payroll_periods.is_active` is a single global flag, but the schema's unique key supports concurrent periods per employment type. If permanent/contractual/temporary run simultaneously, scope `getActive()` by employment type and enforce with a partial unique index.
- **Late-deduction traceability** — `late_deduction_matrix` has no column linking a computed late deduction back to the bracket row applied; add one if an audit trail is required.
- **Tests** — add Pest feature tests per CRUD endpoint and service, using model factories.
- **Change default admin credentials** before any non-local deployment.

---

*Generated 2026-07-14 · Laravel 12 · PHP 8.2 · Filament 5 · MySQL 8.*
