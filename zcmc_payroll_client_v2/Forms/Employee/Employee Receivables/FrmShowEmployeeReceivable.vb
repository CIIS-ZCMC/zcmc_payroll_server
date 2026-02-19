Public Class FrmShowEmployeeReceivable
    Private service As New EmployeeService
    Private serviceEmployeeReceivable As New EmployeeReceivableService

    Public _employeeID As Integer

    Private Sub btnClose_Click(sender As Object, e As EventArgs) Handles btnClose.Click
        Me.Close()
    End Sub

    Private Sub btnMinimize_Click(sender As Object, e As EventArgs) Handles btnMinimize.Click
        Me.WindowState = FormWindowState.Minimized
    End Sub

    Private Async Sub btnAdd_Click(sender As Object, e As EventArgs) Handles btnAdd.Click
        Dim obj As New AddEditEmployeeReceivable
        obj._employeeID = _employeeID
        obj.add = True
        If obj.ShowDialog() = DialogResult.OK Then
            Await service.ShowEmployeeReceivable(dgvTable, _employeeID)
            obj.Close()
        End If
    End Sub

    Private Async Sub FrmShowEmployeeReceivable_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        Await service.ShowEmployeeReceivable(dgvTable, _employeeID)
    End Sub

    Private Sub dgvTable_CellContentClick(sender As Object, e As DataGridViewCellEventArgs) Handles dgvTable.CellContentClick
        If e.RowIndex >= 0 Then
            'Dim selectedRow As DataGridViewRow = dgvTable.Rows(e.RowIndex)
            'Dim receivableID As Integer = Convert.ToInt32(selectedRow.Cells("ReceivableID").Value)
            'If dgvTable.Columns(e.ColumnIndex).Name = "Edit" Then
            '    Dim obj As New AddEditEmployeeReceivable
            '    obj._receivableID = receivableID
            '    obj.ShowDialog()
            'ElseIf dgvTable.Columns(e.ColumnIndex).Name = "Delete" Then
            '    Dim confirmResult = MessageBox.Show("Are you sure to delete this receivable?", "Confirm Delete", MessageBoxButtons.YesNo)
            '    If confirmResult = DialogResult.Yes Then
            '        serviceEmployeeReceivable.DeleteEmployeeReceivable(receivableID)
            '        MessageBox.Show("Receivable deleted successfully.")
            '        ' Refresh the data grid view
            '        service.ShowEmployeeReceivable(dgvTable, _employeeID)
            '    End If
            'End If
        End If

    End Sub
End Class