Public Class PayrollPeriodService
    Dim helper As New Helpers

    Public Async Function GetAll(dgv As DataGridView) As Task(Of PayrollPeriodResponse)
        Try
            dgv.Rows.Clear()

            Dim response = Await PayrollPeriodApi.GetAll()

            If response Is Nothing OrElse response.data Is Nothing Then Exit Function

            Dim i As Integer = 1
            For Each data As PayrollPeriodResponse In response.data
                dgv.Rows.Add(i, data.id, data.month, data.year, data.period_type, data.employment_type, "Set")
                i += 1
            Next

            dgv.Refresh()

            Return Nothing
        Catch ex As Exception
            Debug.Print(ex.Message)
        End Try
    End Function

    Public Async Function GetActivePayroll(lbl As Label,
                                           hasFilter As Boolean,
                                           Optional employmentType As String = Nothing,
                                           Optional periodType As String = Nothing,
                                           Optional month As Integer = Nothing,
                                           Optional year As Integer = Nothing) As Task(Of PayrollPeriodResponse)
        Try
            Dim response = Await PayrollPeriodApi.GetActivePayroll(hasFilter, employmentType, periodType, month, year)

            Dim data As PayrollPeriodResponse = response.data

            If response IsNot Nothing Then
                AppState.PayrollPeriodId = data.id
                AppState.PayrollPeriod = data
                lbl.Text = $"{helper.GetMonthName(data.month)} {data.year}"
            End If

            Return Nothing
        Catch ex As Exception
            Debug.Print(ex.Message)
        End Try
    End Function
End Class
