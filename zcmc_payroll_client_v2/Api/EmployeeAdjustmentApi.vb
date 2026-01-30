Public Class EmployeeAdjustmentApi
    Public Shared Async Function Create(data As Object) As Task(Of ApiResponse(Of EmployeeAdjustmentResponse))
        Return Await ApiClient.PostAsync(Of EmployeeAdjustmentResponse)(urlEmployeeAdjustment, data)
    End Function

    Public Shared Async Function Show(id As Integer) As Task(Of ApiResponse(Of EmployeeAdjustmentResponse))
        Return Await ApiClient.GetAsync(Of EmployeeAdjustmentResponse)($"{urlEmployeeAdjustment}/{id}")
    End Function
End Class
