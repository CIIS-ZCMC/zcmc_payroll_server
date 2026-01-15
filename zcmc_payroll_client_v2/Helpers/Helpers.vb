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
End Class
