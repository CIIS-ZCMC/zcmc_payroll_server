Public Class ImportDeductionResponse
    Public Property id As Integer
    Public Property uuid As String
    Public Property name As String
    Public Property code As String
    Public Property type As String
    Public Property billing_cycle As String
    Public Property employee_deductions As List(Of EmployeeDeductionResponse)
End Class
