Public Class EmployeeReceivableApi
    Public Shared Async Function GetAll(Optional paginate As Boolean = True, Optional perPage As Integer = 15, Optional page As Integer = 1) As Task(Of ApiResponse(Of List(Of EmployeeReceivableResponse)))
        Dim endpoint As String = $"{urlEmployeeReceivable}?paginate={paginate.ToString().ToLower()}&per_page={perPage}&page={page}"
        Return Await ApiClient.GetAsync(Of List(Of EmployeeReceivableResponse))(endpoint)
    End Function

    Public Shared Async Function Create(data As Object) As Task(Of ApiResponse(Of EmployeeReceivableResponse))
        Return Await ApiClient.PostAsync(Of EmployeeReceivableResponse)(urlEmployeeReceivable, data)
    End Function

    Public Shared Async Function Show(id As Integer) As Task(Of ApiResponse(Of EmployeeReceivableResponse))
        Return Await ApiClient.GetAsync(Of EmployeeReceivableResponse)($"{urlEmployeeReceivable}/{id}")
    End Function

    Public Shared Async Function Update(id As Integer, data As Object) As Task(Of ApiResponse(Of EmployeeReceivableResponse))
        Return Await ApiClient.PutAsync(Of EmployeeReceivableResponse)($"{urlEmployeeReceivable}/{id}", data)
    End Function

    Public Shared Async Function Delete(id As Integer) As Task(Of ApiResponse(Of String))
        Return Await ApiClient.DeleteAsync(Of String)($"{urlEmployeeReceivable}/{id}")
    End Function
End Class
