Public Class GeneralPayrollResponse
    Public Property general_payroll_id As Integer
    Public Property id As Integer
    Public Property generated_by_id As String
    Public Property generated_by_name As String
    Public Property payroll_period As PayrollPeriodResponse
    Public Property total_employees As Integer
    Public Property total_deductions As Decimal
    Public Property total_receivables As Decimal
    Public Property total_gross As Decimal
    Public Property total_net As Decimal
    Public Property month As String
    Public Property year As String
End Class
