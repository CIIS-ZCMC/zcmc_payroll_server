Public Class PayrollProcessResponse
    Public Property id As Integer
    Public Property payroll_period_id As Integer
    Public Property payroll_period As PayrollPeriodResponse
    Public Property payroll_type As String
    Public Property current_step As Integer
    Public Property status As String
    Public Property started_by As String
    Public Property started_at As String
    Public Property created_at As String
    Public Property updated_at As String
End Class
