Public Class PayrollWizardContext
    ' ===== Step 1: Payroll Settings =====
    Public EmploymentType As String
    Public DaysOfDuty As Integer
    Public SalaryPeriod As String
    Public PayrollType As String

    ' ===== Step 2: Selected Employees =====
    Public IncludedEmployeeIds As New List(Of Integer)

    ' ===== Step 3: Generated Payroll Result =====
    Public GeneratedPayrollId As Integer
    Public GeneratedAt As DateTime
End Class
