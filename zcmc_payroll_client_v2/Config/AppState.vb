Public NotInheritable Class AppState
    Private Sub New()
    End Sub

    Public Shared Property PayrollPeriodId As Integer
    Public Shared Property PayrollPeriod As PayrollPeriodResponse
    Public Shared Property PayrollType As Integer
    Public Shared Property CurrentPayrollProcess As PayrollProcessResponse
    Public Shared Property PayrollPeriodContext As PayrollPeriodContext
End Class
