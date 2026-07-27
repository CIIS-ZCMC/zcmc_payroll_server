# Payroll System — Migration Runbook

Companion to [`payroll_schema_migrations.md`](payroll_schema_migrations.md). Follow this top to bottom. The order guarantees every foreign-key target exists before the table that references it.

> All commands run from `server/` (the Laravel root). Pass `--no-interaction` on CI.

---

## 0. Pre-flight

```bash
cd "D:/System/Version 2/ZCMC_PAYROLL/server"

# Confirm environment + DB connection
php artisan --version                 # expect Laravel 12.x
php artisan config:show database.default
php artisan db:show                   # verifies the connection is reachable
```

Back up first if the target DB has data:

```bash
# adjust credentials/db name to your .env
mysqldump -u <user> -p <database> > backup_pre_payroll.sql
```

---

## 1. Generate the migration stubs

Run these in order — the numeric timestamp Laravel prepends preserves execution order, and creating them in this sequence keeps them sorted correctly.

```bash
php artisan make:migration create_deduction_groups_table
php artisan make:migration create_receivable_groups_table
php artisan make:migration create_late_deduction_matrix_table
php artisan make:migration create_deductions_table
php artisan make:migration create_receivables_table
php artisan make:migration create_employees_table
php artisan make:migration create_payroll_periods_table
php artisan make:migration create_payroll_processes_table
php artisan make:migration create_payroll_runs_table
php artisan make:migration create_payroll_summaries_table
php artisan make:migration create_employee_salaries_table
php artisan make:migration create_employee_exclusions_table
php artisan make:migration create_employee_time_records_table
php artisan make:migration create_employee_computed_salaries_table
php artisan make:migration create_employee_deductions_table
php artisan make:migration create_employee_deduction_terms_table
php artisan make:migration create_employee_deduction_payments_table
php artisan make:migration create_employee_deduction_logs_table
php artisan make:migration create_employee_receivables_table
php artisan make:migration create_employee_receivable_terms_table
php artisan make:migration create_employee_receivable_payments_table
php artisan make:migration create_employee_receivable_logs_table
php artisan make:migration create_employee_payrolls_table
php artisan make:migration create_employee_payroll_details_table
php artisan make:migration create_payroll_adjustments_table
php artisan make:migration create_night_differential_rules_table
php artisan make:migration create_night_differential_runs_table
php artisan make:migration create_employee_night_duties_table
php artisan make:migration create_employee_night_differentials_table
php artisan make:migration create_logs_and_trails_table
```

Then fill each stub's `up()` from the column tables in `payroll_schema_migrations.md`.

---

## 2. Column-definition cheatsheet

Map the schema's type column to Blueprint methods:

| Schema type | Blueprint |
|---|---|
| `int` PK | `$table->id();` |
| `int` FK | `$table->foreignId('x_id')->constrained();` (or `->constrained('target_table')` when the name differs) |
| `string` | `$table->string('col');` |
| `text` / `longText` | `$table->text('col');` / `$table->longText('col');` |
| `boolean` | `$table->boolean('col')->default(false);` |
| `date` / `time` / `datetime` | `$table->date/time/dateTime('col');` |
| `timestamp` | `$table->timestamp('col')->nullable();` |
| `decimal(p,s)` | `$table->decimal('col', p, s);` |
| `enum` | `$table->enum('col', [...values]);` |
| `json` | `$table->json('col');` |

Conventions to apply:

- Add `$table->timestamps();` to every table unless a table is purely append-only and you decide otherwise.
- Mark columns noted **nullable** in the schema with `->nullable()`.
- **Self-referencing FK** (`payroll_runs.is_reversed_from` → `payroll_runs.id`): declare the column first, then add the FK after row creation to avoid a chicken-and-egg constraint:
  ```php
  $table->foreignId('is_reversed_from')->nullable()
        ->constrained('payroll_runs')->nullOnDelete();
  ```
- **Unique constraints** — add each `**Unique:**` line from the schema:
  ```php
  $table->unique(['employment_type', 'month', 'year', 'payroll_type']);
  ```
- **Actor FKs** (`started_by_id`, `generated_by_id`, `computed_by_id`, `created_by_id`, `approved_by_id`, `locked_by_id`, `action_by_id`): the schema leaves these un-FK'd (Open Item). Define them as plain `unsignedBigInteger(...)->nullable()` until the users-table relationship is confirmed. **Do not** blindly `constrained()` them.

---

## 3. Run the migrations

```bash
# Preview the SQL without touching the DB
php artisan migrate --pretend

# Apply
php artisan migrate

# Verify
php artisan migrate:status
```

If something fails mid-run, fix the offending stub and re-run `php artisan migrate` (already-applied migrations are skipped).

---

## 4. Reset / iterate during development

```bash
php artisan migrate:rollback        # undo the last batch
php artisan migrate:fresh           # drop all tables + re-run (DESTROYS DATA)
php artisan migrate:fresh --seed    # + run seeders
```

> `migrate:fresh` is destructive. Never run it against production.

---

## 5. Post-migration checks

```bash
# Confirm foreign keys landed
php artisan db:table payroll_runs
php artisan db:table employee_night_differentials
```

Spot-check that each `**Unique:**` and FK from `payroll_schema_migrations.md` is present.

---

## Open items to resolve before production

- **Actor reference fields** are not FK-constrained — confirm they point to a users table and add constraints if so.
- **`late_deduction_matrix`** has no column linking a computed late deduction back to the bracket row applied — add a traceability column if an audit trail is required.
