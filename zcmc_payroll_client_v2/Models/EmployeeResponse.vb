Public Class EmployeeResponse
    Public Property id As Integer
    Public Property employee_profile_id As Integer
    Public Property employee_number As String
    Public Property full_name As String
    Public Property first_name As String
    Public Property last_name As String
    Public Property middle_name As String
    Public Property ext_name As String
    Public Property designation As String
    Public Property assigned_area As AssignedAreaResponse
    Public Property status As Integer
    Public Property is_newly_hired As Integer
    Public Property is_excluded As Integer
    Public Property is_resigned As Integer
    Public Property salary As EmployeeSalaryResponse
    Public Property deductions As List(Of EmployeeDeductionResponse)
    Public Property receivables As List(Of EmployeeReceivableResponse)
    Public Property employee_time_records As EmployeeTimeRecordResponse
    Public Property excluded As ExcludedEmployeeResponse
End Class
