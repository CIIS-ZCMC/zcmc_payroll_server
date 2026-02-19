Imports System.Runtime.Remoting.Contexts

Public Class FrmPayrollType
    Public Property ResultSession As PayrollSession

    Private servicePayrollProcess As New PayrollProcessService
    Private servicePayrollPeriod As New PayrollPeriodService
    Private serviceFetchEmployee As New FetchEmployeeService
    Private serviceFetchProgress As New FetchProgressService

    Private helper As New Helpers

    Private Async Sub btnRegularPayroll_Click(sender As Object, e As EventArgs) Handles btnRegularPayroll.Click
        Await StartPayroll(PayrollTypes.regular)
    End Sub

    Private Async Sub btnJobOrderPayroll_Click(sender As Object, e As EventArgs) Handles btnJobOrderPayroll.Click
        Await StartPayroll(PayrollTypes.job_order)
    End Sub

    Private Async Sub btnNightDifferential_Click(sender As Object, e As EventArgs) Handles btnNightDifferential.Click
        Await StartPayroll(PayrollTypes.night_differential)
    End Sub

    Private Async Sub btnSpecialPayroll_Click(sender As Object, e As EventArgs) Handles btnSpecialPayroll.Click
        Await StartPayroll(PayrollTypes.special)
    End Sub

    Private Async Sub btn13MonthPay_Click(sender As Object, e As EventArgs) Handles btn13MonthPay.Click
        Await StartPayroll(PayrollTypes.thirteen_month)
    End Sub

    Private Sub ToggleButtons(enabled As Boolean)
        btnRegularPayroll.Enabled = enabled
        btnJobOrderPayroll.Enabled = enabled
        btnNightDifferential.Enabled = enabled
        btnSpecialPayroll.Enabled = enabled
        btn13MonthPay.Enabled = enabled
    End Sub

    Private Async Function StartPayroll(type As PayrollTypes) As Task
        ToggleButtons(False)

        Try
            Dim context As PayrollPeriodContext

            Try
                context = ValidateAndBuild(type)
            Catch ex As Exception
                MessageBox.Show(ex.Message)
                Return
            End Try

            context.payroll_type = type

            ''Get Payroll Period
            Dim PayrollPeriod = Await servicePayrollPeriod.GetPayrollPeriod(True, context)

            ''If payroll period doesn't exist, fetch employee data and create payroll period again
            If PayrollPeriod Is Nothing Then
                'MessageBox.Show("Unable to retrieve payroll period.")
                'Return
                Dim fetch = Await serviceFetchEmployee.Create(context)

                If Not fetch.Success Then
                    MessageBox.Show("Failed to start employee fetching.")
                    Return
                End If

                Dim obj As New FrmLoading
                obj.Show()

                Try
                    Dim timeoutMs As Integer = 300000 ' 5 minutes safety timeout
                    Dim elapsed As Integer = 0

                    Do
                        Dim progress = Await serviceFetchProgress.GetProgress()

                        If progress Is Nothing Then
                            Continue Do
                        End If

                        obj.StartLoading($"Fetching Data... {progress.percentage}%")

                        If progress.status = "completed" Then
                            Exit Do
                        End If

                        If progress.status = "failed" Then
                            Throw New Exception("Employee fetching failed on server.")
                        End If

                        Await Task.Delay(500) ' Poll every 500ms
                        elapsed += 500

                        If elapsed >= timeoutMs Then
                            Throw New TimeoutException("Fetching employees timed out.")
                        End If
                    Loop

                    ''Insert fetched employee and create payroll period to local database
                    Dim insertCache = Await serviceFetchEmployee.Fetch(context)
                Finally

                    obj.StopLoading()
                    obj.Close()
                End Try
            End If

            ' Refetch PayrollPeriod After fetching is done
            PayrollPeriod = Await servicePayrollPeriod.GetPayrollPeriod(True, context)

            If PayrollPeriod Is Nothing Then
                MessageBox.Show("Payroll period still not found after fetching.")
                Return
            End If

            ''Get or Create Payroll Process
            Dim process = Await servicePayrollProcess.GetOrCreateProcess(PayrollPeriod.id, PayrollPeriod.payroll_type)

            If process Is Nothing Then
                MessageBox.Show("Unable to start payroll process.")
                Return
            End If

            ResultSession = New PayrollSession With {
                .PayrollPeriodId = PayrollPeriod.id,
                .PayrollPeriod = PayrollPeriod,
                .PayrollType = type,
                .CurrentPayrollProcess = process
            }

            ' Close form with OK result
            Me.DialogResult = DialogResult.OK
            Me.Close()

        Finally
            ApplyEmploymentTypeRules()
        End Try
    End Function

    Private Function ValidateAndBuild(type As PayrollTypes) As PayrollPeriodContext

        If cmbEmploymentType.SelectedIndex = -1 Then
            Throw New Exception("Please select an Employment Type.")
        End If

        If cmbMonth.SelectedIndex = -1 Then
            Throw New Exception("Please select a Month.")
        End If

        If cmbYear.SelectedIndex = -1 Then
            Throw New Exception("Please select a Year.")
        End If

        Dim _employmentType As String = If(cmbEmploymentType.SelectedItem = "Regular", "regular", "job_order")
        Dim _periodType As String = If(rdbFirstHalf.Checked, "first_half", "second_half")
        Dim _month As Integer = helper.GetMonthNumber(cmbMonth.SelectedItem)
        Dim _year As Integer = CInt(cmbYear.SelectedItem)

        Return New PayrollPeriodContext With {
            .employment_type = _employmentType,
            .payroll_type = type,
            .period_type = _periodType,
            .month = _month,
            .year = _year
        }
    End Function

    Private Sub cmbEmploymentType_SelectedIndexChanged(sender As Object, e As EventArgs) Handles cmbEmploymentType.SelectedIndexChanged
        ApplyEmploymentTypeRules()
    End Sub

    Private Sub ApplyEmploymentTypeRules()

        btnRegularPayroll.Enabled = False
        btnJobOrderPayroll.Enabled = False
        btnNightDifferential.Enabled = False
        btnSpecialPayroll.Enabled = False
        btn13MonthPay.Enabled = False

        If cmbEmploymentType.SelectedItem Is Nothing Then Exit Sub

        Dim value = cmbEmploymentType.SelectedItem.ToString()

        Select Case value
            Case "Regular"
                btnRegularPayroll.Enabled = True


            Case "Job Order"
                btnJobOrderPayroll.Enabled = True
        End Select

        btnNightDifferential.Enabled = True
        btnSpecialPayroll.Enabled = True
        btn13MonthPay.Enabled = True

    End Sub
End Class