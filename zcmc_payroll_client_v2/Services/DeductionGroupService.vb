Public Class DeductionGroupService
    Public Async Function GetAll(dgv As DataGridView, paginate As Boolean, Optional perPage As Integer = 15, Optional page As Integer = 1) As Task(Of DeductionGroupResponse)
        Try
            dgv.Rows.Clear()

            Dim response = Await DeductionGroupApi.GetAll(paginate, perPage, page)

            If response Is Nothing OrElse response.data Is Nothing Then Exit Function

            Dim i As Integer = 1
            For Each data As DeductionGroupResponse In response.data
                dgv.Rows.Add(i, data.id, data.name, data.code, "Edit", "Delete")
                i += 1
            Next

            dgv.Refresh()

            Return Nothing
        Catch ex As Exception
            Debug.Print(ex.Message)
        End Try
    End Function
End Class
