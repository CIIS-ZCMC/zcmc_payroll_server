Public Class PayrollWizardContext
    ' ===== Step 1: Payroll Settings =====
    Public Property EmploymentType As String
    Public Property DaysOfDuty As Integer
    Public Property SalaryPeriod As String
    Public Property PayrollType As String

    ' ===== Step 2: Selected Employees =====
    Public Property IncludedEmployeeIds As New List(Of Integer)

    ' ===== Step 2.5: Payroll Preview Data =====
    Public Property EmployeePayrolls As New List(Of EmployeePayrollDto)

    ' ===== Step 3: Generated Payroll Result =====
    Public Property GeneratedPayrollId As Integer
    Public Property GeneratedAt As DateTime
End Class
