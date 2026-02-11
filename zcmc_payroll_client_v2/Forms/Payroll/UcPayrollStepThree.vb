Imports System.Linq.Expressions

Public Class UcPayrollStepThree
    Public Property Context As PayrollWizardContext
    Public Property Pagination As New PaginationContext()

    Private preview As New EmployeePreviewService
    Private service As New EmployeePayrollService

    Dim helper As New Helpers

    Public Async Function IsValid() As Task(Of Boolean)
        ' validate step logic
        Dim result = MessageBox.Show(
                "You are about to proceed to generate payroll . Have you completed managing all deductions and receivables?",
                "Confirm Action",
                MessageBoxButtons.YesNo,
                MessageBoxIcon.Question
            )

        If result = DialogResult.No Then
            Return False
        End If

        Using obj As New FrmAuthorizationPin
            If obj.ShowDialog() = DialogResult.OK Then
                Dim res = Await StoreEmployeePayroll()

                If res = True Then
                    obj.Close()
                    Return True
                End If
            End If
        End Using

        Return True
    End Function

    Private Async Sub UcPayrollStepThree_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        Dim ActivePayroll = AppState.PayrollPeriod
        lblPayrollType.Text = $"{Context.EmploymentType.ToString()} - {Context.PayrollType.ToString()}"
        lblPeriod.Text = $"{helper.GetMonthName(ActivePayroll.month)} {ActivePayroll.period_start} - {ActivePayroll.period_end}, {ActivePayroll.year}"

        Await LoadEmployees()
    End Sub

    Private Async Sub cmbPerPage_SelectedIndexChanged(sender As Object, e As EventArgs) Handles cmbPerPage.SelectedIndexChanged
        If cmbPerPage.SelectedItem Is Nothing Then Exit Sub

        Pagination.PerPage = CInt(cmbPerPage.SelectedItem)
        Pagination.Page = 1

        Await LoadEmployees()
    End Sub

    Private Async Sub cmbPage_SelectedIndexChanged(sender As Object, e As EventArgs) Handles cmbPage.SelectedIndexChanged
        If cmbPage.SelectedItem Is Nothing Then Exit Sub

        Pagination.Page = CInt(cmbPage.SelectedItem)
        Await LoadEmployees()
    End Sub

    Private Async Sub btnPrevious_Click(sender As Object, e As EventArgs) Handles btnPrevious.Click
        If Pagination.Page <= 1 Then Exit Sub

        Pagination.Page -= 1
        Await LoadEmployees()
    End Sub

    Private Async Sub btnNext_Click(sender As Object, e As EventArgs) Handles btnNext.Click
        If Pagination.Page >= Pagination.LastPage Then Exit Sub

        Pagination.Page += 1
        Await LoadEmployees()
    End Sub

    Private Async Function LoadEmployees() As Task
        Await LoadingHelper.RunAsync(
            Async Function()
                Await preview.GetSelected(dgvTable, lblMessage, Context.IncludedEmployeeIds, Pagination, Context.EmployeePayrolls)
            End Function,
            True
        )

        helper.UpdatePaginationControls(Pagination, cmbPage, lblPerPage, lblPage, btnPrevious, btnNext, AddressOf cmbPage_SelectedIndexChanged)
    End Function

    Private Async Function StoreEmployeePayroll() As Task(Of Boolean)
        Dim loadingForm As New FrmLoading

        Try
            loadingForm.Show()
            loadingForm.StartLoading("Saving payroll...")

            Dim res = Await service.Create("general", Context.EmployeePayrolls)

            If res.Success Then
                Return True
            Else
                MessageBox.Show(res.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
                Return False
            End If

        Catch ex As Exception
            MessageBox.Show(ex.Message, "Exception", MessageBoxButtons.OK, MessageBoxIcon.Error)
            Return False

        Finally
            loadingForm.StopLoading()
            loadingForm.Close()
        End Try
    End Function
End Class
