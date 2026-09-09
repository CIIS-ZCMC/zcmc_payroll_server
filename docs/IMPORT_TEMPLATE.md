# Payroll Import Template — canonical layout and cleanup

Step 1 of the general payroll imports one file per deduction or receivable. This document
is the specification the importer enforces, and the cleanup list for the files currently in
circulation.

The decision behind it: **the source files get standardised; the importer stays strict.** Cell
B1 must hold the exact code from the deduction/receivable catalog. The importer does not keep
an alias table and does not guess — it names the nearest catalog codes in its error and stops.

---

## 1. Layout

| Row | Contents |
|---|---|
| 1 | `Code` in A1 (optional label), **the catalog code in B1** (required) |
| 2 | `Date` in A2, month name in B2, year in C2 — **optional, but all-or-nothing** |
| 3 | Column headers: `Seq. No.` \| `Employee No.` \| `Fullname` \| `Amount` \| `Term (months)` \| `Months Paid` |
| 4+ | One row per employee |

Columns are read by position, not by header text:

| Column | Field | Notes |
|---|---|---|
| A | Sequence number | Ignored |
| B | **Employee No.** | Matched against `employees.employee_number`. Read as text, so `2023010333` from an .xlsx and `"2023010333"` from a .csv both work. A blank ends the row. |
| C | Fullname | Ignored — for the human reading the sheet |
| D | **Amount** | See §2 |
| E | Term (months) | Optional. Greater than zero makes the deduction term-based |
| F | Months Paid | Optional. Only meaningful with a term |

**Row 2 is optional.** Every `.xlsx` template in use leaves it blank; the `.csv` exports carry
`Date | February | 2025`. When it is present the importer checks it against the payroll period
being imported into and rejects a mismatch — a February file cannot be imported into a March
payroll. When it is blank the operator's chosen period is taken at face value. Half a
declaration (a month with no year, or the reverse) is an error.

**Data starts on row 4.** Row 3 must be the column header row with something containing
"Employee" in column B. If it is not, the importer says so rather than reporting every
employee in the file as missing.

---

## 2. Amounts

| Amount | Meaning | What the importer does |
|---|---|---|
| A positive number | The employee owes this | Insert, or update the existing row for the period |
| **`0` or blank** | The employee owes nothing for this deduction this period | **Clears any carried-forward amount to 0**, and flags the row for review |
| Anything else (`n/a`, text) | Unusable | Skipped and reported |

Zero is not an error — it is pervasive and normal. Whole files arrive at zero (`D10-GMPL`,
`D16-COOP1` and `D21-DBP` in the February set; 39 of 41 rows in August's `D04-GCONS`).

The file is treated as authoritative, so a `0` against an employee who has a carried-forward
amount **clears it**. Because that changes take-home pay, every such row comes back in the
import response under `zeroed`, with the previous amount and the employee number. A `0` against
an employee with nothing carried forward changes nothing and is counted under `no_charge`
rather than flagged — otherwise a normal file would produce forty warnings.

> Once the `import_batches` staging lands (plan §Step 1), these become
> acknowledge-before-commit warnings. Until then they are applied and reported, because the
> alternative is changing someone's pay silently.

---

## 3. Terms

`Term (months)` and `Months Paid` were previously ignored entirely. They are now applied:

- Term **greater than zero** → `with_terms = true`, `total_term` = the term, `total_paid` =
  Months Paid (0 when blank).
- Term **blank or zero** → the sheet is saying nothing about terms, so an existing
  amortisation is left exactly as it is. This matters: the old importer wrote a hardcoded
  `with_terms = 0` on every update, which turned a term-based loan into a flat deduction and
  lost its schedule.

`employee_receivables` has no term columns — only `total_paid` — so columns E and F are ignored
for receivable files.

---

## 4. Other rules

- **Duplicates.** Two rows for the same employee number in one file: the first wins, the second
  is reported. Previously the unique index meant the later row silently overwrote the earlier
  one with nothing to say which amount survived. February's `D01-WTAX.csv` has exactly this —
  rows 44–45 repeat sequence 39 and 40.
- **Unknown employees** are reported per row, not written to the log and forgotten.
- **A locked period** cannot be imported into.
- Trailing blank rows are ignored.

---

## 5. Cleanup: what B1 must be changed to

Every code observed across the four sample months. `php artisan payroll:audit-import-files
<folder>` regenerates this for any folder.

### Resolves already — leave alone

`TAX` · `PHIC` · `GCAL` · `GPOL` · `GEDU` · `GFAL` · `GMPL` · `PMPL` · `PCAL` · `PAG2` ·
`ADUES` · `DEATH` · `CHAPEL` · `DBP` · `PERA` · `HAZARD` · `CELL`

### Unambiguous — a spelling difference only

| In the files | Catalog code | Catalog name |
|---|---|---|
| `WTAX` | `TAX` | Withholding Tax |
| `PHILHEALTH` | `PHIC` | PHILHEALTH Premium |
| `GCONS`, `GSIS CONSO` | `GCONSO` | GSIS Consolidated Loan |
| `GSIS GCAL` | `GCAL` | GSIS Calamity |
| `GSIS GPOL` | `GPOL` | GSIS Policy |
| `GSIS GFAL` | `GFAL` | GSIS Financial Assistance Loan |
| `GSIS GMPL` | `GMPL` | GSIS Multipurpose Loan |
| `GSIS EDU`, `EDU` | `GEDU` | GSIS Educational Loan |
| `GCOMPUTER`, `GCOMP`, `COMPUTER`, `GSIS CPL` | `GCOM` | GSIS Computer Loan |
| `GSIS-PREMIUM`, `GSIS PREMIUM`, `GSIS PS`, `GSIS` | `GSIS L & R` | GSIS Premium |
| `PAGIBIG-PREMIUM`, `HDMF Premium`, `HDMF`, `PAGIBIG` | `PPREM.` | Pag-Ibig Premium |
| `HDMF PMPL`, `P-MPL` | `PMPL` | Pag-Ibig Multipurpose Loan |
| `HDMF PCAL` | `PCAL` | Pag-Ibig Calamity Loan |
| `PHOUSING`, `HDMF HOUSING`, `P-HOUSING` | `PHL` | Pag-Ibig Housing Loan |
| `HDMF PAG2`, `MP2`, `PMP2` | `PAG2` | Pag-Ibig 2 Savings |
| `DUES` | `ADUES` | Association Dues |
| `Chapel` | `CHAPEL` | Chapel Voluntary Contribution |
| `CELLPHONE` | `CELL` | (receivable) |
| `COOP1`, `COOP 1` | `CML` | Canteen Money Loan — see §5.1 |
| `COOP3`, `COOP 3` | `CFL` | Canteen Food/Groceries Loan — see §5.1 |

### 5.1 COOP1 and COOP3 are the canteen loans, not the coop loans

Worth stating explicitly, because the obvious guess is wrong and the importer's
"did you mean" hint will make it: `COOP1` is **not** `COOPL1`. String distance says it is;
the data says otherwise.

- **August, identical data.** `D16-COOP1.xlsx` matches `Canteen Money Loan August 2024.xlsx`
  employee-for-employee and amount-for-amount across all 10 non-zero rows. `D17-COOP 3.xlsx`
  matches `Canteen Food Loan August2024.xlsx` across all 3. Both source files sit in
  `Deductions/Other loans, contributions, and dues/` in the same folder.
- **October, independent confirmation.** `D14-COOP1.xlsx` reproduces the master workbook's
  *Canteen Money Loan* column exactly (`200, 500×6, 2266.67, 2833.33, 7108.69`), and
  `D15-COOP3.xlsx` its *Canteen Food Loan* column (`335, 1500, 1804`).
- **Column position.** August's master workbook carries `COOP1 | TERM (COOP1) | COOP3 |
  TERM (COOP3)` in the same slot where October's carries `Canteen Money Loan | Canteen Food
  Loan`. The same two deductions, renamed between months.

`COOPL1` ("Coop 4 Loan 1") and `COOPL2` ("Coop 5 Loan 2") are referenced by **no file** in any
of the four sample months. They are unused catalog rows. Either retire them, or find out from
the payroll office what they were for — but do not point the COOP files at them.

Since this is the payroll office's own data, confirm the mapping with them before the first
live run. It changes which deduction real amounts post against.

### Needs someone to decide

| In the files | Problem |
|---|---|
| `TOTAL DEDUCTIONS`, `GROSS INCOME`, `NAME` | Not deductions at all — these are summary/source workbooks (`D-TOTAL DEDUCTIONS.xlsx`, `R-GROSS INCOME.xlsx`, `Receivables and Deductions.xlsx`) sitting in the same folder. They should not be uploaded; consider moving them out of the import folder. |
| August `R01-PERA.xlsx` | Malformed — no code in B1 and no employee numbers, only a column of amounts. October's `R01-PERA.xlsx` is correct; use that as the model. |
| September `D04-GCONS.xlsx` | B1 is empty. |

---

## 6. Tooling

**Check a month's folder before importing:**

```bash
php artisan payroll:audit-import-files "C:/Users/khdolar/Documents/Payroll Sample Deductions/10_OCTOBER DEDUCTION PAYROLL"
```

Reports every file's B1 code, whether it resolves, the nearest catalog codes when it does not,
and whether the layout is right. Exits non-zero when anything needs attention, so it can gate a
batch.

**Generate a correct blank template:**

```bash
php artisan payroll:import-template TAX --month=February --year=2026 --out=storage/app/import-templates
```

Writes the canonical layout with a verified catalog code already in B1 and one row per
employee, so B1 is never typed by hand. `--format=csv` for a CSV; omit `--month`/`--year` to
leave row 2 blank.
