Public Class FetchEmployeeApi
    Public Shared Async Function FetchOrCreate(context As PayrollPeriodContext) As Task(Of ApiResponse(Of ServiceResult))
        Dim endpoint As String = $"{urlFetchEmployee}?year={context.year}&month={context.month}&employment_type={context.employment_type}&period_type={context.period_type}"
        Return Await ApiClient.GetAsync(Of ServiceResult)(endpoint)
    End Function

    Public Shared Async Function Create(data As Object) As Task(Of ApiResponse(Of ServiceResult))
        Return Await ApiClient.PostAsync(Of ServiceResult)(urlFetchEmployee, data)
    End Function
End Class
