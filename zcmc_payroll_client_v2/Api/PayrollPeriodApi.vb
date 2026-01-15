Public Class PayrollPeriodApi
    Public Shared Async Function GetAll() As Task(Of ApiResponse(Of List(Of PayrollPeriodResponse)))
        Dim endpoint As String = $"{urlPayrollPeriod}?has_filter={False}"
        Return Await ApiClient.GetAsync(Of List(Of PayrollPeriodResponse))(endpoint)
    End Function

    Public Shared Async Function GetActivePayroll(hasFilter As Boolean,
                                              Optional employmentType As String = Nothing,
                                              Optional periodType As String = Nothing,
                                              Optional month As Integer = Nothing,
                                              Optional year As Integer = Nothing) As Task(Of ApiResponse(Of PayrollPeriodResponse))

        Dim endpoint As String = $"{urlPayrollPeriod}?has_filter={hasFilter}&employment_type={employmentType}&period_type={periodType}&month={month}&year={year}"
        Return Await ApiClient.GetAsync(Of PayrollPeriodResponse)(endpoint)
    End Function
End Class
