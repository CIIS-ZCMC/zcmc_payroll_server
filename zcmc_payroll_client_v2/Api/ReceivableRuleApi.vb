Public Class ReceivableRuleApi
    Public Shared Async Function GetAll(Optional paginate As Boolean = False, Optional perPage As Integer = 15, Optional page As Integer = 1) As Task(Of ApiResponse(Of List(Of ReceivableRuleResponse)))
        Dim endpoint As String = $"{urlReceivableRule}?paginate={paginate.ToString().ToLower()}&per_page={perPage}&page={page}"
        Return Await ApiClient.GetAsync(Of List(Of ReceivableRuleResponse))(endpoint)
    End Function

    Public Shared Async Function Create(data As Object) As Task(Of ApiResponse(Of ReceivableRuleResponse))
        Return Await ApiClient.PostAsync(Of ReceivableRuleResponse)(urlReceivableRule, data)
    End Function

    Public Shared Async Function Show(id As Integer) As Task(Of ApiResponse(Of ReceivableRuleResponse))
        Return Await ApiClient.GetAsync(Of ReceivableRuleResponse)($"{urlReceivableRule}/{id}")
    End Function

    Public Shared Async Function Update(id As Integer, data As Object) As Task(Of ApiResponse(Of ReceivableRuleResponse))
        Return Await ApiClient.PutAsync(Of ReceivableRuleResponse)($"{urlReceivableRule}/{id}", data)
    End Function

    Public Shared Async Function Delete(id As Integer) As Task(Of ApiResponse(Of String))
        Return Await ApiClient.DeleteAsync(Of String)($"{urlReceivableRule}/{id}")
    End Function
End Class
