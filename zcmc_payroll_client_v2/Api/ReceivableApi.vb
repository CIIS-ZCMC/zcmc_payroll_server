Public Class ReceivableApi
    Public Shared Async Function GetAll(Optional paginate As Boolean = True, Optional perPage As Integer = 15, Optional page As Integer = 1) As Task(Of ApiResponse(Of List(Of ReceivableResponse)))
        Dim endpoint As String = $"{urlReceivable}?paginate={paginate.ToString().ToLower()}&per_page={perPage}&page={page}"
        Return Await ApiClient.GetAsync(Of List(Of ReceivableResponse))(endpoint)
    End Function

    Public Shared Async Function Create(data As Object) As Task(Of ApiResponse(Of ReceivableResponse))
        Return Await ApiClient.PostAsync(Of ReceivableResponse)(urlReceivable, data)
    End Function

    Public Shared Async Function Show(id As Integer) As Task(Of ApiResponse(Of ReceivableResponse))
        Return Await ApiClient.GetAsync(Of ReceivableResponse)($"{urlReceivable}/{id}")
    End Function

    Public Shared Async Function Update(id As Integer, data As Object) As Task(Of ApiResponse(Of ReceivableResponse))
        Return Await ApiClient.PutAsync(Of ReceivableResponse)($"{urlReceivable}/{id}", data)
    End Function

    Public Shared Async Function Delete(id As Integer) As Task(Of ApiResponse(Of String))
        Return Await ApiClient.DeleteAsync(Of String)($"{urlReceivable}/{id}")
    End Function
End Class
