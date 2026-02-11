Public Class EmployeePayrollDto
    Public Property employee_id As Integer
    Public Property employee_time_record_id As Integer?
    Public Property payroll_period_id As Integer
    Public Property month As String
    Public Property year As String
    Public Property basic_pay As Decimal
    Public Property total_receivables As Decimal
    Public Property gross_pay As Decimal
    Public Property total_deductions As Decimal
    Public Property net_pay As Decimal
End Class
