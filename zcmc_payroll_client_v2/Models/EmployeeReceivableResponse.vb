Public Class EmployeeReceivableResponse
    Public Property id As Integer
    Public Property employee As EmployeeResponse
    Public Property payroll_period As PayrollPeriodResponse
    Public Property receivable As ReceivableResponse
    Public Property employee_id As Integer
    Public Property payroll_period_id As Integer
    Public Property receivable_id As Integer
    Public Property amount As Decimal
    Public Property percentage As Decimal
    Public Property frequency As String
    Public Property date_from As String
    Public Property date_to As String
    Public Property total_paid As Integer
    Public Property reason As String
    Public Property status As String
    Public Property is_default As String
    Public Property stopped_at As String
    Public Property completed_at As String
End Class
