Public Class FrmShowEmployeeDeduction
    Private service As New EmployeeService
    Private serviceEmployeeDeduction As New EmployeeDeductionService

    Public _employeeID As Integer

    Private Sub btnClose_Click(sender As Object, e As EventArgs) Handles btnClose.Click
        Me.Close()
    End Sub

    Private Sub btnMinimize_Click(sender As Object, e As EventArgs) Handles btnMinimize.Click
        Me.WindowState = FormWindowState.Minimized
    End Sub

    Private Async Sub btnAdd_Click(sender As Object, e As EventArgs) Handles btnAdd.Click
        Dim obj As New AddEditEmployeeDeduction
        obj._employeeID = _employeeID
        obj.add = True
        If obj.ShowDialog() = DialogResult.OK Then
            Await service.ShowEmployeeDeduction(dgvTable, _employeeID)
            obj.Close()
        End If
    End Sub

    Private Async Sub FrmShowEmployeeDeduction_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        Await service.ShowEmployeeDeduction(dgvTable, _employeeID)
    End Sub

    Private Async Sub dgvTable_CellContentClick(sender As Object, e As DataGridViewCellEventArgs) Handles dgvTable.CellContentClick
        Dim row = dgvTable.SelectedRows(0)

        If e.ColumnIndex = 17 Then
            Dim obj As New AddEditEmployeeDeduction
            obj.edit = True
            obj._dgv = dgvTable
            If obj.ShowDialog() = DialogResult.OK Then
                Await service.ShowEmployeeDeduction(dgvTable, _employeeID)
                obj.Close()
            End If
        ElseIf e.ColumnIndex = 18 Then
            Dim res = MessageBox.Show("Are you sure you want to stop this deduction?", "Confirm Delete", MessageBoxButtons.YesNo, MessageBoxIcon.Question)
            If res = DialogResult.Yes Then
                Dim obj As New FrmAuthorizationPin
                If obj.ShowDialog() = DialogResult.OK Then
                    obj.Close()

                    Dim id = row.Cells(1).Value

                    Dim dto As New EmployeeDeductionDto With {
                        .PayrollPeriodId = row.Cells(2).Value,
                        .EmployeeId = row.Cells(3).Value,
                        .DeductionId = row.Cells(4).Value,
                        .Frequency = row.Cells(13).Value,
                        .Reason = row.Cells(14).Value,
                        .IsDefault = row.Cells(15).Value
                    }

                    Await serviceEmployeeDeduction.Update(id, dto, "toStop")
                    MessageBox.Show("Deduction stopped successfully.", "Success", MessageBoxButtons.OK, MessageBoxIcon.Information)
                End If
            End If
        End If
    End Sub
End Class