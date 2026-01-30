Public Class EmployeePreviewResponse
    Public Property id As Integer
    Public Property employee_number As String
    Public Property full_name As String
    Public Property designation As String
    Public Property assigned_area As AssignedAreaResponse
    Public Property reason As String
    Public Property status As String
    Public Property payroll As PayrollRecords
End Class

Public Class PayrollRecords
    Public Property payroll_period_id As Integer
    Public Property employee_time_record_id As Integer
    Public Property total_receivables As Decimal
    Public Property total_deductions As Decimal
    Public Property basic_pay As Decimal
    Public Property gross_pay As Decimal
    Public Property net_pay As Decimal
    Public Property currency As String
End Class
