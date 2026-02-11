Imports System.Security
Imports System.Windows.Forms.VisualStyles.VisualStyleElement.StartPanel

Public Class UcPayrollStepTwo
    Dim helper As New Helpers

    Public Property Context As PayrollWizardContext
    Public Property Pagination As New PaginationContext()

    Private service As New EmployeePreviewService

    Private isRestoring As Boolean = False
    Private isRowClickToggle As Boolean = False

    Public Async Function IsValid() As Task(Of Boolean)
        If Context.IncludedEmployeeIds.Count = 0 Then
            MessageBox.Show("Please select at least one employee for the payroll.", "Message", MessageBoxButtons.OK, MessageBoxIcon.Warning)
            Return False
        End If

        Return True
    End Function

    Private Async Sub UcPayrollStepTwo_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        cmbPerPage.SelectedIndex = 0

        Pagination.PerPage = CInt(cmbPerPage.SelectedItem)
        Pagination.Page = 1

        AddHandler dgvTable.CurrentCellDirtyStateChanged, AddressOf dgvTable_CurrentCellDirtyStateChanged
        AddHandler dgvTable.CellValueChanged, AddressOf dgvTable_CellValueChanged

        RestoreCheckedState()
    End Sub

    Private Sub dgvTable_CurrentCellDirtyStateChanged(sender As Object, e As EventArgs) Handles dgvTable.CurrentCellDirtyStateChanged
        If dgvTable.IsCurrentCellDirty Then
            dgvTable.CommitEdit(DataGridViewDataErrorContexts.Commit)
        End If
    End Sub

    Private Sub dgvTable_CellValueChanged(sender As Object, e As DataGridViewCellEventArgs)
        If isRestoring Then Exit Sub

        If e.RowIndex < 0 Then Exit Sub
        If dgvTable.Columns(e.ColumnIndex).Name <> "action_select" Then Exit Sub

        Dim row = dgvTable.Rows(e.RowIndex)
        Dim employeeId As Integer = CInt(row.Cells(2).Value)
        Dim isChecked As Boolean = CBool(row.Cells(0).Value)

        If isChecked Then
            AddEmployee(employeeId)
        Else
            RemoveEmployee(employeeId)
        End If

    End Sub

    Private Sub AddEmployee(employeeId As Integer)
        If Not Context.IncludedEmployeeIds.Contains(employeeId) Then
            Context.IncludedEmployeeIds.Add(employeeId)
        End If
    End Sub

    Private Sub RemoveEmployee(employeeId As Integer)
        Context.IncludedEmployeeIds.Remove(employeeId)
    End Sub

    Private Sub RestoreCheckedState()
        isRestoring = True

        For Each row As DataGridViewRow In dgvTable.Rows
            Dim employeeId As Integer = CInt(row.Cells(2).Value)

            If Context.IncludedEmployeeIds.Contains(employeeId) Then
                row.Cells(0).Value = True
            Else
                row.Cells(0).Value = False
            End If
        Next

        isRestoring = False
    End Sub

    Private Sub dgvTable_CellContentClick(sender As Object, e As DataGridViewCellEventArgs) Handles dgvTable.CellContentClick
        If e.RowIndex < 0 Then Exit Sub

        ' Ignore direct checkbox clicks (they already toggle themselves)
        If dgvTable.Columns(e.ColumnIndex).Name = "action_select" Then Exit Sub

        Dim row As DataGridViewRow = dgvTable.Rows(e.RowIndex)
        Dim cell = row.Cells("action_select")

        Dim currentValue As Boolean = False
        If cell.Value IsNot Nothing Then
            currentValue = CBool(cell.Value)
        End If

        isRowClickToggle = True
        cell.Value = Not currentValue
        isRowClickToggle = False
    End Sub

    Private Sub btnSelectAll_Click(sender As Object, e As EventArgs) Handles btnSelectAll.Click
        For Each row As DataGridViewRow In dgvTable.Rows
            Dim employeeId As Integer = CInt(row.Cells(2).Value)

            row.Cells(0).Value = True

            If Not Context.IncludedEmployeeIds.Contains(employeeId) Then
                Context.IncludedEmployeeIds.Add(employeeId)
            End If
        Next
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
                Await service.GetIncluded(dgvTable, lblMessage, Nothing, Pagination)
            End Function,
            True
        )

        helper.UpdatePaginationControls(Pagination, cmbPage, lblPerPage, lblPage, btnPrevious, btnNext, AddressOf cmbPage_SelectedIndexChanged)
        RestoreCheckedState()
    End Function
End Class
