Public Class DeductionRuleApi
    Public Shared Async Function GetAll(Optional paginate As Boolean = False, Optional perPage As Integer = 15, Optional page As Integer = 1) As Task(Of ApiResponse(Of List(Of DeductionRuleResponse)))
        Dim endpoint As String = $"{urlDeductionRule}?paginate={paginate.ToString().ToLower()}&per_page={perPage}&page={page}"
        Return Await ApiClient.GetAsync(Of List(Of DeductionRuleResponse))(endpoint)
    End Function

    Public Shared Async Function Create(data As Object) As Task(Of ApiResponse(Of DeductionRuleResponse))
        Return Await ApiClient.PostAsync(Of DeductionRuleResponse)(urlDeductionRule, data)
    End Function

    Public Shared Async Function Show(id As Integer) As Task(Of ApiResponse(Of DeductionRuleResponse))
        Return Await ApiClient.GetAsync(Of DeductionRuleResponse)($"{urlDeductionRule}/{id}")
    End Function

    Public Shared Async Function Update(id As Integer, data As Object) As Task(Of ApiResponse(Of DeductionRuleResponse))
        Return Await ApiClient.PutAsync(Of DeductionRuleResponse)($"{urlDeductionRule}/{id}", data)
    End Function

    Public Shared Async Function Delete(id As Integer) As Task(Of ApiResponse(Of String))
        Return Await ApiClient.DeleteAsync(Of String)($"{urlDeductionRule}/{id}")
    End Function
End Class
