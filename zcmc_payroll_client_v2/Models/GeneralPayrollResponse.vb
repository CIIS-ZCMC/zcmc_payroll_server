Public Class GeneralPayrollResponse
    Public Property id As Integer
    Public Property payroll_period_id As Integer
    Public Property payroll_period As PayrollPeriodResponse
    Public Property generated_by_id As String
    Public Property generated_by_name As String
    Public Property total_employees As Integer
    Public Property total_deductions As Decimal
    Public Property total_receivables As Decimal
    Public Property total_gross As Decimal
    Public Property total_net As Decimal
    Public Property total_night_differential As Decimal
    Public Property deleted_at As String
    Public Property created_at As String
    Public Property updated_at As String
End Class
