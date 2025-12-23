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

End Class
