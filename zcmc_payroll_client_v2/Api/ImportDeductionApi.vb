Public Class ImportDeductionApi
    Public Shared Async Function GetAll(Optional paginate As Boolean = False, Optional perPage As Integer = 15, Optional page As Integer = 1) As Task(Of ApiResponse(Of List(Of ImportDeductionResponse)))
        Dim endpoint As String = $"{urlDeduction}?paginate={paginate.ToString().ToLower()}&per_page={perPage}&page={page}"
        Return Await ApiClient.GetAsync(Of List(Of ImportDeductionResponse))(endpoint)
    End Function

    Public Shared Async Function Show(id As Integer, toImport As Boolean) As Task(Of ApiResponse(Of ImportDeductionResponse))
        Return Await ApiClient.GetAsync(Of ImportDeductionResponse)($"{urlDeduction}/{id}?payroll_period_id={AppState.PayrollPeriodId}&import={toImport}")
    End Function
End Class
