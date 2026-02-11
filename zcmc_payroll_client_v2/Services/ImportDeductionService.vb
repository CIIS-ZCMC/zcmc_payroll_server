Public Class ImportDeductionService
    Public Async Function GetAll(dgv As DataGridView, paginate As Boolean, Optional perPage As Integer = 15, Optional page As Integer = 1) As Task(Of DeductionResponse)
        Try
            dgv.Rows.Clear()

            Dim response = Await ImportDeductionApi.GetAll(paginate, perPage, page)

            If response Is Nothing OrElse response.data Is Nothing Then Exit Function

            For Each data As ImportDeductionResponse In response.data
                dgv.Rows.Add(data.id, data.name, "Import", "Clear")
            Next

            dgv.Refresh()

            Return Nothing
        Catch ex As Exception
            Debug.Print(ex.Message)
        End Try
    End Function

    Public Async Function Show(dgv As DataGridView, id As Integer, lbl As Label) As Task
        Try
            lbl.Visible = False
            lbl.Text = ""

            dgv.Rows.Clear()

            Dim response = Await ImportDeductionApi.Show(id, True)
            Dim data As ImportDeductionResponse = response.data

            If data.employee_deductions Is Nothing OrElse data.employee_deductions.Count = 0 Then
                lbl.Visible = True
                lbl.Text = "No Records Found"
                Exit Function
            End If

            For Each item In data.employee_deductions
                ' ComboBox setup
                Dim comboCell As New DataGridViewComboBoxCell()
                comboCell.Items.AddRange("absolute", "differential")

                Dim isDifferential As Boolean = Convert.ToBoolean(item.isDifferential)
                comboCell.Value = If(isDifferential, "differential", "absolute")

                ' Checkbox setup
                Dim willDeduct As Boolean = False
                If item.deduct_at IsNot Nothing Then
                    willDeduct = Convert.ToBoolean(item.deduct_at)
                End If

                Dim rowIndex As Integer = dgv.Rows.Add(
                    item.id,
                    item.employee.employee_number,
                    item.employee.full_name,
                    item.amount,
                    item.total_term,
                    item.total_paid,
                    Nothing,     ' ComboBox placeholder
                    willDeduct   ' Checkbox
                )

                dgv.Rows(rowIndex).Cells(6) = comboCell 'For ComboBox Value
            Next

        Catch ex As Exception
            Debug.Print(ex.Message)
        End Try
    End Function
End Class
