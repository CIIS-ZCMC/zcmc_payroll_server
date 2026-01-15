Public Class UcManageDeductionGroup
    Private deductionGroupService As New DeductionGroupService()

    Private Async Sub UcManageDeductionGroup_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        Await deductionGroupService.GetAll(dgvTable, False)
    End Sub

    Private Sub btnAdd_Click(sender As Object, e As EventArgs) Handles btnAdd.Click
        Dim obj As New AddEditDeductionGroup()
        If obj.ShowDialog() = DialogResult.OK Then
            obj.action = "add"
            dgvTable.Rows.Clear()
        End If
    End Sub

    Private Sub dgvTable_CellContentClick(sender As Object, e As DataGridViewCellEventArgs) Handles dgvTable.CellContentClick

    End Sub

End Class
