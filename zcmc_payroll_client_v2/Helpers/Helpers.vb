Public Class Helpers
    Public Sub RenderUserControl(pnl As Panel, uc As UserControl)
        pnl.SuspendLayout()
        pnl.Controls.Clear()

        uc.Dock = DockStyle.Fill
        pnl.Controls.Add(uc)

        pnl.ResumeLayout()
        pnl.PerformLayout()
        uc.PerformLayout()
    End Sub

    Public Function GetMonthName(month As Integer) As String
        Return New DateTime(1, month, 1).ToString("MMMM")
    End Function

    Public Function BindDropDown(cmb As ComboBox, data As IList(Of ComboItem), Optional defaultText As String = Nothing)
        cmb.BeginUpdate()

        cmb.DataSource = Nothing
        cmb.DisplayMember = NameOf(ComboItem.Text)
        cmb.ValueMember = NameOf(ComboItem.Id)

        If Not String.IsNullOrEmpty(defaultText) Then
            data.Insert(0, New ComboItem With {
                .Id = 0,
                .Text = defaultText
            })
        End If

        cmb.DataSource = data
        cmb.EndUpdate()
    End Function

    Public Function CheckDgvRows(dgvTable As DataGridView, lbl As Label)
        If dgvTable.Rows.Count = 0 Then
            lbl.Visible = True
            lbl.Text = "No data is found"
        Else
            lbl.Visible = False
        End If
    End Function

    Public Sub UpdatePaginationControls(pagination As PaginationContext, cmbPage As ComboBox, lblPerPage As Label, lblPage As Label,
                                        btnPrevious As Button, btnNext As Button, pageChangedHandler As EventHandler)

        If cmbPage.Items.Count = 0 OrElse cmbPage.Items.Count <> pagination.LastPage Then
            cmbPage.Items.Clear()
            For i As Integer = 1 To pagination.LastPage
                cmbPage.Items.Add(i)
            Next
        End If

        If cmbPage.SelectedItem Is Nothing OrElse CInt(cmbPage.SelectedItem) <> pagination.Page Then
            RemoveHandler cmbPage.SelectedIndexChanged, pageChangedHandler
            cmbPage.SelectedItem = pagination.Page
            AddHandler cmbPage.SelectedIndexChanged, pageChangedHandler
        End If

        btnPrevious.Enabled = pagination.Page > 1
        btnNext.Enabled = pagination.Page < pagination.LastPage

        Dim startRecord As Integer = ((pagination.Page - 1) * pagination.PerPage) + 1
        Dim endRecord As Integer = Math.Min(pagination.Page * pagination.PerPage, pagination.Total)

        If pagination.Total = 0 Then
            lblPerPage.Text = "No records found"
        Else
            lblPerPage.Text = $"{startRecord}-{endRecord} of {pagination.Total} employees"
        End If

        lblPage.Text = $"of {pagination.LastPage}"
    End Sub

    Public Function GetMonthNumber(monthName As String) As Integer
        Dim dt As DateTime = DateTime.ParseExact(monthName, "MMMM", Globalization.CultureInfo.InvariantCulture)
        Return dt.Month
    End Function
End Class
