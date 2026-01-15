Public Class FrmShowEmployeeDeduction
    Private service As New EmployeeService

    Public _employeeID As Integer

    Private Sub btnClose_Click(sender As Object, e As EventArgs) Handles btnClose.Click
        Me.Close()
    End Sub

    Private Sub btnMinimize_Click(sender As Object, e As EventArgs) Handles btnMinimize.Click
        Me.WindowState = FormWindowState.Minimized
    End Sub

    Private Sub btnAdd_Click(sender As Object, e As EventArgs) Handles btnAdd.Click
        Dim obj As New AddEditEmployeeDeduction
        obj._employeeID = _employeeID
        obj.add = True
        obj.ShowDialog()
    End Sub

    Private Async Sub FrmShowEmployeeDeduction_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        Await service.Show(dgvTable, _employeeID)
    End Sub

    Private Sub dgvTable_CellContentClick(sender As Object, e As DataGridViewCellEventArgs) Handles dgvTable.CellContentClick
        If e.ColumnIndex = 16 Then
            'EDIT


        ElseIf e.ColumnIndex = 17 Then
            'DELETE

        End If
    End Sub
End Class