Public Class FrmSettings
    Dim helper As New Helpers

    Private isSettingsExpanded As Boolean = False
    Private Const SETTINGS_EXPANDED_HEIGHT As Integer = 150
    Private Const ANIMATION_STEP As Integer = 10

    Private Sub FrmSettings_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        pnlSettingsMenu.Height = 0
    End Sub
    Private Sub btnClose_Click(sender As Object, e As EventArgs) Handles btnClose.Click
        Me.Close()
    End Sub

    Private Sub btnMinimize_Click(sender As Object, e As EventArgs) Handles btnMinimize.Click
        Me.WindowState = FormWindowState.Minimized
    End Sub

    Private Sub btnReports_Click(sender As Object, e As EventArgs) Handles btnReports.Click
        helper.RenderUserControl(panelContent, New UcReport)
    End Sub

    Private Sub btnRefetchEmployee_Click(sender As Object, e As EventArgs) Handles btnRefetchEmployee.Click
        helper.RenderUserControl(panelContent, New UcEmployeeRefetch)
    End Sub

    Private Sub btnSettings_Click(sender As Object, e As EventArgs) Handles btnSettings.Click
        tmrSettings.Start()
    End Sub

    Private Sub btnDeductionGroups_Click(sender As Object, e As EventArgs) Handles btnDeductionGroups.Click
        helper.RenderUserControl(panelContent, New UcManageDeductionGroup)
    End Sub

    Private Sub btnDeductions_Click(sender As Object, e As EventArgs) Handles btnDeductions.Click
        helper.RenderUserControl(panelContent, New UcManageDeduction)

    End Sub

    Private Sub btnReceivables_Click(sender As Object, e As EventArgs) Handles btnReceivables.Click
        helper.RenderUserControl(panelContent, New UcManageReceivable)
    End Sub


    Private Sub btnLogout_Click(sender As Object, e As EventArgs) Handles btnLogout.Click

    End Sub

    Private Sub tmrSettings_Tick(sender As Object, e As EventArgs) Handles tmrSettings.Tick
        If isSettingsExpanded Then
            ' COLLAPSE
            pnlSettingsMenu.Height -= ANIMATION_STEP
            If pnlSettingsMenu.Height <= 0 Then
                pnlSettingsMenu.Height = 0
                isSettingsExpanded = False
                tmrSettings.Stop()
            End If
        Else
            ' EXPAND
            pnlSettingsMenu.Height += ANIMATION_STEP
            If pnlSettingsMenu.Height >= SETTINGS_EXPANDED_HEIGHT Then
                pnlSettingsMenu.Height = SETTINGS_EXPANDED_HEIGHT
                isSettingsExpanded = True
                tmrSettings.Stop()
            End If
        End If
    End Sub
End Class