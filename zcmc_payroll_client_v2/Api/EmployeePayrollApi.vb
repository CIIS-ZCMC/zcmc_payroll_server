Public Class EmployeePayrollApi
    Public Shared Async Function Create(data As Object) As Task(Of ApiResponse(Of EmployeePayrollResponse))
        Return Await ApiClient.PostAsync(Of EmployeePayrollResponse)(urlEmployeePayroll, data)
    End Function
End Class
