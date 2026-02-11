Public Class UcPayrollStepOne
    Public Property Context As PayrollWizardContext

    Public Async Function IsValid() As Task(Of Boolean)
        If Not ValidateInputs() Then
            Return False
        End If

        SetInputs()

        Return True
    End Function

    Private Sub UcPayrollStepOne_Load(sender As Object, e As EventArgs) Handles MyBase.Load

    End Sub

    Private Function ValidateInputs() As Boolean
        If Not rdbGeneralPayroll.Checked AndAlso Not rdbSpecialPayroll.Checked Then
            MessageBox.Show("Please select payroll type.", "Message", MessageBoxButtons.OK, MessageBoxIcon.Warning)
            Return False
        End If

        If Not rdbRegular.Checked AndAlso Not rdbJobOrder.Checked Then
            MessageBox.Show("Please select employment type.", "Message", MessageBoxButtons.OK, MessageBoxIcon.Warning)
            Return False
        End If

        Dim employmentType As String = If(rdbJobOrder.Checked, "job order", "regular")

        If employmentType = "job order" Then
            If Not rdb22Days.Checked AndAlso Not rdb24Days.Checked Then
                MessageBox.Show("Please select days of duty.", "Message", MessageBoxButtons.OK, MessageBoxIcon.Warning)
                Return False
            End If

            If Not rdb1to15.Checked AndAlso Not rdb16to31.Checked Then
                MessageBox.Show("Please select salary period.", "Message", MessageBoxButtons.OK, MessageBoxIcon.Warning)
                Return False
            End If
        End If

        Return True
    End Function

    Private Sub SetInputs()
        If rdbRegular.Checked Then
            Context.EmploymentType = "Regular"
        ElseIf rdbJobOrder.Checked Then
            Context.EmploymentType = "Job Order"
        End If

        If rdb22Days.Checked Then
            Context.DaysOfDuty = 22
        ElseIf rdb24Days.Checked Then
            Context.DaysOfDuty = 24
        End If

        If rdb1to15.Checked Then
            Context.SalaryPeriod = "1-15"
        ElseIf rdb16to31.Checked Then
            Context.SalaryPeriod = "16-31"
        End If

        If rdbGeneralPayroll.Checked Then
            Context.PayrollType = "General"
        ElseIf rdbSpecialPayroll.Checked Then
            Context.PayrollType = "Special"
        End If
    End Sub
End Class
