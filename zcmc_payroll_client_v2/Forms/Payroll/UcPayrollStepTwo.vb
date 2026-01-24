Public Class UcPayrollStepTwo
    Public Property Context As PayrollWizardContext

    Private service As New EmployeeAdjustmentService

    Public Function IsValid() As Boolean
        If Context.IncludedEmployeeIds.Count = 0 Then
            MessageBox.Show("Please select at least one employee for the payroll.", "Message", MessageBoxButtons.OK, MessageBoxIcon.Warning)
            Return False
        End If

        Return True
    End Function

    Private Async Sub UcPayrollStepTwo_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        Await LoadingHelper.RunAsync(
            Async Function()
                Await service.GetEmployeeAdjustmentWithCheckbox(dgvTable, lblMessage, "isIncluded", False)
            End Function,
            True
        )

        RestoreCheckedState()
    End Sub

    Private Sub dgvTable_CurrentCellDirtyStateChanged(sender As Object, e As EventArgs) Handles dgvTable.CurrentCellDirtyStateChanged
        If dgvTable.IsCurrentCellDirty Then
            dgvTable.CommitEdit(DataGridViewDataErrorContexts.Commit)
        End If
    End Sub

    Private Sub dgvTable_CellValueChanged(sender As Object, e As DataGridViewCellEventArgs) Handles dgvTable.CellValueChanged
        If e.RowIndex < 0 Then Exit Sub
        If dgvTable.Columns(e.ColumnIndex).Name <> "action_select" Then Exit Sub

        Dim row As DataGridViewRow = dgvTable.Rows(e.RowIndex)

        Dim isChecked As Boolean = False
        If row.Cells(0).Value IsNot Nothing Then
            isChecked = CBool(row.Cells(0).Value)
        End If

        Dim employeeId As Integer = CInt(row.Cells(2).Value)

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
        For Each row As DataGridViewRow In dgvTable.Rows
            Dim employeeId As Integer = CInt(row.Cells(2).Value)

            If Context.IncludedEmployeeIds.Contains(employeeId) Then
                row.Cells(0).Value = True
            Else
                row.Cells(0).Value = False
            End If
        Next
    End Sub
End Class
