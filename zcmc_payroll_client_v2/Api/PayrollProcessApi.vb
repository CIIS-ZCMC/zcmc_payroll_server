Public Class PayrollProcessApi
    Public Shared Async Function Create(data As Object) As Task(Of ApiResponse(Of PayrollProcessResponse))
        Return Await ApiClient.PostAsync(Of PayrollProcessResponse)(urlPayrollProcess, data)
    End Function

    Public Shared Async Function Show(payrollPeriodId As Integer, payrollType As Integer) As Task(Of ApiResponse(Of PayrollProcessResponse))
        Dim endpoint As String = $"{urlPayrollProcess}/{payrollPeriodId}?payroll_type={payrollType}"
        Return Await ApiClient.GetAsync(Of PayrollProcessResponse)(endpoint)
    End Function

    Public Shared Async Function Update(id As Integer, data As Object) As Task(Of ApiResponse(Of PayrollProcessResponse))
        Return Await ApiClient.PutAsync(Of PayrollProcessResponse)($"{urlPayrollProcess}/{id}", data)
    End Function
End Class
