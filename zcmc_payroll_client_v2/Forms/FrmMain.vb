Public Class FrmMain
    Private Sub FrmMain_Load(sender As Object, e As EventArgs) Handles MyBase.Load

    End Sub

    Private Sub btnDashboard_Click(sender As Object, e As EventArgs) Handles btnDashboard.Click

    End Sub

    Private Sub btnImports_Click(sender As Object, e As EventArgs) Handles btnImports.Click

    End Sub

    Private Sub btnEmployee_Click(sender As Object, e As EventArgs) Handles btnEmployee.Click
        RenderUserControl(New UcManageEmployee())
    End Sub

    Private Sub btnGeneralPayroll_Click(sender As Object, e As EventArgs) Handles btnGeneralPayroll.Click

    End Sub

    Private Sub btnSettings_Click(sender As Object, e As EventArgs) Handles btnSettings.Click

    End Sub

    Private Sub btnLogout_Click(sender As Object, e As EventArgs) Handles btnLogout.Click

    End Sub

    Private Sub RenderUserControl(uc As UserControl)
        panelContent.SuspendLayout()
        panelContent.Controls.Clear()

        uc.Dock = DockStyle.Fill
        panelContent.Controls.Add(uc)

        panelContent.ResumeLayout()
        panelContent.PerformLayout()
        uc.PerformLayout()
    End Sub

End Class