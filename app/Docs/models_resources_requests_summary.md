# Payroll System — Models, Resources & Requests Summary

Complete scaffolding for the payroll system created on 2026-07-13. All files formatted with Pint and follow Laravel best practices.

---

## 30 Eloquent Models

Located in `app/Models/`, all with proper relationships, fillable attributes, and casts:

### Core Setup
- `DeductionGroup` — Groups deductions into categories
- `ReceivableGroup` — Groups receivables into categories
- `Employee` — Core employee master data

### Payroll Core
- `PayrollPeriod` — Payroll cycle definition (month, year, type, status)
- `PayrollRun` — Payroll execution instance with version control
- `PayrollProcess` — Multi-step payroll processing workflow
- `PayrollSummary` — Aggregated payroll totals by run

### Employee Salary & Attendance
- `EmployeeSalary` — Salary assignment per payroll period
- `EmployeeTimeRecord` — Time tracking (hours, absences, OT, ND)
- `EmployeeComputedSalary` — Calculated pay rates (daily, hourly, per-minute)
- `EmployeeExclusion` — Exclusions from payroll runs

### Deductions
- `Deduction` — Deduction type definition
- `EmployeeDeduction` — Employee-level deduction assignment
- `EmployeeDeductionTerm` — Installment tracking (total, paid, balance)
- `EmployeeDeductionPayment` — Per-payrun payment record
- `EmployeeDeductionLog` — Audit trail for deductions

### Receivables
- `Receivable` — Receivable type definition
- `EmployeeReceivable` — Employee-level receivable assignment
- `EmployeeReceivableTerm` — Installment tracking (total, paid, balance)
- `EmployeeReceivablePayment` — Per-payrun payment record
- `EmployeeReceivableLog` — Audit trail for receivables

### Payroll Output
- `EmployeePayroll` — Final payroll for employee per run
- `EmployeePayrollDetail` — Line-item breakdown (deductions, OT, ND, etc.)
- `PayrollAdjustment` — Post-run adjustments (bonus, penalty, correction, reversal)

### Night Differential
- `NightDifferentialRule` — Rules by employment type & time window
- `NightDifferentialRun` — Batch computation per period
- `EmployeeNightDuty` — Night shift record by date
- `EmployeeNightDifferential` — Computed ND pay

### Audit
- `LateDeductionMatrix` — Late deduction bracket rules
- `LogAndTrail` — Complete audit log of all actions

---

## 11 API Resources

Located in `app/Http/Resources/`, all with proper nested relationships and date formatting:

- `EmployeeResource` — Employee with computed full name
- `PayrollRunResource` — Run with nested period
- `PayrollPeriodResource` — Period with formatted dates
- `EmployeePayrollResource` — Payroll with employee, period, run details
- `DeductionResource` — Deduction with group name
- `ReceivableResource` — Receivable with group name
- `EmployeeSalaryResource` — Salary with employee & period
- `EmployeeTimeRecordResource` — Time record with key metrics
- `EmployeeDeductionResource` — Deduction with dates formatted
- `EmployeeReceivableResource` — Receivable with dates formatted
- `PayrollSummaryResource` — Summary totals with run details

All resources use `whenLoaded()` to prevent N+1 queries.

---

## 20 Form Requests

Located in `app/Http/Requests/`, all with full validation rules:

### Employee
- `StoreEmployeeRequest` — Create: unique employee_number, all required fields
- `UpdateEmployeeRequest` — Update: unique employee_number excluding current

### Payroll Runs
- `StorePayrollRunRequest` — Create: version, status validation
- `UpdatePayrollRunRequest` — Update: no version change

### Payroll Periods
- `StorePayrollPeriodRequest` — Create: unique (employment_type, month, year, payroll_type); date order validation
- `UpdatePayrollPeriodRequest` — Update: same uniqueness rules

### Employee Payrolls
- `StoreEmployeePayrollRequest` — Create: FK validation, numeric minimums
- `UpdateEmployeePayrollRequest` — Update: value consistency checks

### Deductions & Receivables
- `StoreDeductionRequest` / `UpdateDeductionRequest` — Deduction CRUD
- `StoreReceivableRequest` / `UpdateReceivableRequest` — Receivable CRUD

### Employee Salary
- `StoreEmployeeSalaryRequest` — Create: employment_type enum, base_salary > 0
- `UpdateEmployeeSalaryRequest` — Update: same rules

### Employee Time Records
- `StoreEmployeeTimeRecordRequest` — Create: all numeric fields > 0
- `UpdateEmployeeTimeRecordRequest` — Update: same rules

### Employee Deductions & Receivables
- `StoreEmployeeDeductionRequest` — Create: effective_date < end_date, status enum
- `UpdateEmployeeDeductionRequest` — Update: same rules
- `StoreEmployeeReceivableRequest` — Create: effective_date < end_date, status enum
- `UpdateEmployeeReceivableRequest` — Update: same rules

All requests use `Rule::in()` for enum validation and `exists:` for foreign key validation.

---

## Key Features Implemented

✓ **Relationships** — All 30 models have proper HasMany, BelongsTo, and HasOne relationships
✓ **Casts** — Decimal, boolean, date, and datetime casts configured
✓ **Fillable** — All models have whitelist of fillable attributes
✓ **Validation** — 20 form requests with:
  - Required/nullable field rules
  - Numeric minimums (no negative salaries, etc.)
  - Date ordering (end_date > effective_date)
  - Enum validation via `Rule::in()`
  - Foreign key existence checks
  - Unique constraints with duplication handling

✓ **Resource Nesting** — Resources load related data with `whenLoaded()` to prevent N+1 queries
✓ **Full Name Computation** — Employee resource generates full_name on-the-fly
✓ **Pint Formatting** — All 61 files formatted to project standards

---

## Next Steps

1. **Controllers** — Create resource controllers to handle CRUD operations
2. **Routes** — Register API routes for all endpoints (recommend version prefix: `/api/v1/`)
3. **Tests** — Pest feature tests for each CRUD operation
4. **Seeders** — Factory + seeder for development/testing data
5. **Policies** — Authorization policies for employee access control
6. **Scope Queries** — Add scopes to models for filtering (e.g., active deductions, current payroll period)

---

**Generated:** 2026-07-13 with Laravel 12 on MySQL 8.0.45
