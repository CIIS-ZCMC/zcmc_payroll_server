Public Class EmployeeDeductionApi
    Public Shared Async Function GetAll(Optional paginate As Boolean = True, Optional perPage As Integer = 15, Optional page As Integer = 1) As Task(Of ApiResponse(Of List(Of EmployeeDeductionResponse)))
        Dim endpoint As String = $"{urlEmployeeDeduction}?paginate={paginate.ToString().ToLower()}&per_page={perPage}&page={page}"
        Return Await ApiClient.GetAsync(Of List(Of EmployeeDeductionResponse))(endpoint)
    End Function

    Public Shared Async Function Create(data As Object) As Task(Of ApiResponse(Of EmployeeDeductionResponse))
        Return Await ApiClient.PostAsync(Of EmployeeDeductionResponse)(urlEmployeeDeduction, data)
    End Function

    Public Shared Async Function Show(id As Integer) As Task(Of ApiResponse(Of EmployeeDeductionResponse))
        Return Await ApiClient.GetAsync(Of EmployeeDeductionResponse)($"{urlEmployeeDeduction}/{id}")
    End Function

    Public Shared Async Function Update(id As Integer, data As Object) As Task(Of ApiResponse(Of EmployeeDeductionResponse))
        Return Await ApiClient.PutAsync(Of EmployeeDeductionResponse)($"{urlEmployeeDeduction}/{id}", data)
    End Function

    Public Shared Async Function Delete(id As Integer) As Task(Of ApiResponse(Of String))
        Return Await ApiClient.DeleteAsync(Of String)($"{urlEmployeeDeduction}/{id}")
    End Function
End Class
