Public Class DeductionApi
    Public Shared Async Function GetAll(Optional paginate As Boolean = True, Optional perPage As Integer = 15, Optional page As Integer = 1) As Task(Of ApiResponse(Of List(Of DeductionResponse)))
        Dim endpoint As String = $"{urlDeduction}?paginate={paginate.ToString().ToLower()}&per_page={perPage}&page={page}"
        Return Await ApiClient.GetAsync(Of List(Of DeductionResponse))(endpoint)
    End Function

    Public Shared Async Function Create(data As Object) As Task(Of ApiResponse(Of DeductionResponse))
        Return Await ApiClient.PostAsync(Of DeductionResponse)(urlDeduction, data)
    End Function

    Public Shared Async Function Show(id As Integer) As Task(Of ApiResponse(Of DeductionResponse))
        Return Await ApiClient.GetAsync(Of DeductionResponse)($"{urlDeduction}/{id}")
    End Function

    Public Shared Async Function Update(id As Integer, data As Object) As Task(Of ApiResponse(Of DeductionResponse))
        Return Await ApiClient.PutAsync(Of DeductionResponse)($"{urlDeduction}/{id}", data)
    End Function

    Public Shared Async Function Delete(id As Integer) As Task(Of ApiResponse(Of String))
        Return Await ApiClient.DeleteAsync(Of String)($"{urlDeduction}/{id}")
    End Function
End Class
