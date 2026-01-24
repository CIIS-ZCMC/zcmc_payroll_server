Public Class EmployeeAdjustmentApi
    Public Shared Async Function GetAll(payroll_period_id As Integer, Optional type As String = "", Optional paginate As Boolean = True, Optional perPage As Integer = 15, Optional page As Integer = 1) As Task(Of ApiResponse(Of List(Of EmployeeAdjustmentResponse)))
        Dim endpoint As String = $"{urlEmployeeAdjustment}?payroll_period_id={payroll_period_id}&type={type}&paginate={paginate.ToString().ToLower()}&per_page={perPage}&page={page}"
        Return Await ApiClient.GetAsync(Of List(Of EmployeeAdjustmentResponse))(endpoint)
    End Function

    Public Shared Async Function Create(data As Object) As Task(Of ApiResponse(Of EmployeeAdjustmentResponse))
        Return Await ApiClient.PostAsync(Of EmployeeAdjustmentResponse)(urlEmployeeAdjustment, data)
    End Function

    Public Shared Async Function Show(id As Integer) As Task(Of ApiResponse(Of EmployeeAdjustmentResponse))
        Return Await ApiClient.GetAsync(Of EmployeeAdjustmentResponse)($"{urlEmployeeAdjustment}/{id}")
    End Function
End Class
