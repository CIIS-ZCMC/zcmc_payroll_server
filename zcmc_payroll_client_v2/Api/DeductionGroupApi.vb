Public Class DeductionGroupApi
    Public Shared Async Function GetAll(Optional paginate As Boolean = False, Optional perPage As Integer = 15, Optional page As Integer = 1) As Task(Of ApiResponse(Of List(Of DeductionGroupResponse)))
        Dim endpoint As String = $"{urlDeductionGroup}?paginate={paginate.ToString().ToLower()}&per_page={perPage}&page={page}"
        Return Await ApiClient.GetAsync(Of List(Of DeductionGroupResponse))(endpoint)
    End Function

    Public Shared Async Function Create(data As Object) As Task(Of ApiResponse(Of DeductionGroupResponse))
        Return Await ApiClient.PostAsync(Of DeductionGroupResponse)(urlDeductionGroup, data)
    End Function

    Public Shared Async Function Show(id As Integer) As Task(Of ApiResponse(Of DeductionGroupResponse))
        Return Await ApiClient.GetAsync(Of DeductionGroupResponse)($"{urlDeductionGroup}/{id}")
    End Function

    Public Shared Async Function Update(id As Integer, data As Object) As Task(Of ApiResponse(Of DeductionGroupResponse))
        Return Await ApiClient.PutAsync(Of DeductionGroupResponse)($"{urlDeductionGroup}/{id}", data)
    End Function

    Public Shared Async Function Delete(id As Integer) As Task(Of ApiResponse(Of String))
        Return Await ApiClient.DeleteAsync(Of String)($"{urlDeductionGroup}/{id}")
    End Function
End Class
