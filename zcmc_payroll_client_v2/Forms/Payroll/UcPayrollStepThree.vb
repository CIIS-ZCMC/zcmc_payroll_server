Public Class UcPayrollStepThree
    Public Property Context As PayrollWizardContext
    Public Property Pagination As New PaginationContext()

    Private service As New EmployeePreviewService

    Dim helper As New Helpers

    Public Function IsValid() As Boolean
        ' validate step logic
        Return True
    End Function

    Private Async Sub UcPayrollStepThree_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        Dim ActivePayroll = AppState.PayrollPeriod
        lblPayrollType.Text = $"{Context.EmploymentType.ToString()} - {Context.PayrollType.ToString()}"
        lblPeriod.Text = $"{helper.GetMonthName(ActivePayroll.month)} {ActivePayroll.period_start} - {ActivePayroll.period_end}, {ActivePayroll.year}"

        'Await service.GetSelected(dgvTable, lblMessage, Context.IncludedEmployeeIds, Pagination)
        Await LoadEmployees()
    End Sub

    Private Sub dgvTable_CellContentClick(sender As Object, e As DataGridViewCellEventArgs) Handles dgvTable.CellContentClick

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
                Await service.GetSelected(dgvTable, lblMessage, Context.IncludedEmployeeIds, Pagination)
            End Function,
            True
        )

        helper.UpdatePaginationControls(Pagination, cmbPage, lblPerPage, lblPage, btnPrevious, btnNext, AddressOf cmbPage_SelectedIndexChanged)
    End Function
End Class
