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

            If response Is Nothing Then
                Debug.Print(response.message)
            End If

            Return response.data

            'Dim data As PayrollPeriodResponse = response.data

            'If response IsNot Nothing Then
            '    AppState.PayrollPeriodId = data.id
            '    AppState.PayrollPeriod = data

            '    Dim period_type As String = ""
            '    If data.period_type = "first_half" Then
            '        period_type = "First Half"
            '    ElseIf data.period_type = "second_half" Then
            '        period_type = "Second Half"
            '    End If

            '    lbl.Text = $"{helper.GetMonthName(data.month)} {data.year}: {period_type}"
            'End If

            'Return Nothing
        Catch ex As Exception
            Debug.Print(ex.Message)
        End Try
    End Function

    Public Async Function GetPayrollPeriod(hasFilter As Boolean, context As PayrollPeriodContext) As Task(Of PayrollPeriodResponse)
        Try
            Dim response = Await PayrollPeriodApi.GetActivePayroll(hasFilter, context.employment_type, context.period_type,
                                                                   context.month, context.year)

            If response Is Nothing Then
                Debug.Print(response.message)
            End If

            Return response.data

        Catch ex As Exception
            Debug.Print(ex.Message)
        End Try
    End Function

    Public Function FormatPayrollPeriodDisplay(period As PayrollPeriodResponse) As String
        Dim periodTypeDisplay As String = If(period.period_type = "first_half", "First Half", "Second Half")
        Return $"{helper.GetMonthName(period.month)} {period.year}: {periodTypeDisplay}"
    End Function
End Class
