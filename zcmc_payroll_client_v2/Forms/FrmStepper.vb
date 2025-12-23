Imports MaterialSkin

Public Class FrmStepper
    Private currentStep As Integer = 1
    Private Const TOTAL_STEPS As Integer = 8

    Private Sub FrmStepper_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        tblpStepper.Controls.Add(CreateStepper(1, "Import"), 0, 0)
        tblpStepper.Controls.Add(CreateStepper(2, "Deductions"), 1, 0)
        tblpStepper.Controls.Add(CreateStepper(3, "Receivables"), 2, 0)
        tblpStepper.Controls.Add(CreateStepper(4, "Excluded"), 3, 0)
        tblpStepper.Controls.Add(CreateStepper(5, "Set"), 4, 0)
        tblpStepper.Controls.Add(CreateStepper(6, "Choose"), 5, 0)
        tblpStepper.Controls.Add(CreateStepper(7, "Generate"), 6, 0)
        tblpStepper.Controls.Add(CreateStepper(8, "View"), 7, 0)

        LoadStep(1) ' default
    End Sub

    Private Sub btnNext_Click(sender As Object, e As EventArgs) Handles btnNext.Click
        If currentStep < TOTAL_STEPS Then
            If Not CanProceed(currentStep) Then Exit Sub
            LoadStep(currentStep + 1)
        End If
    End Sub

    Private Sub btnBack_Click(sender As Object, e As EventArgs) Handles btnBack.Click
        If currentStep > 1 Then
            LoadStep(currentStep - 1)
        End If
    End Sub

    Private Function CreateStepper(stepIndex As Integer, text As String) As Panel
        Dim pnl As New Panel With {
        .Dock = DockStyle.Fill,
        .Tag = stepIndex
    }

        Dim pic As New PictureBox With {
        .Dock = DockStyle.Top,
        .Height = 40,
        .Image = My.Resources.round_processing_32,
        .SizeMode = PictureBoxSizeMode.Zoom
    }

        Dim lbl As New Label With {
        .Dock = DockStyle.Fill,
        .Text = text,
        .TextAlign = ContentAlignment.TopCenter,
        .Font = New Font("Segoe UI", 9.5F)
    }

        pnl.Controls.Add(lbl)
        pnl.Controls.Add(pic)

        Return pnl
    End Function


    Private Sub RenderUserControl(uc As UserControl)
        pnlUserControlDisplay.SuspendLayout()
        pnlUserControlDisplay.Controls.Clear()

        uc.Dock = DockStyle.Fill
        pnlUserControlDisplay.Controls.Add(uc)

        pnlUserControlDisplay.ResumeLayout()
        pnlUserControlDisplay.PerformLayout()
        uc.PerformLayout()
    End Sub

    Private Sub LoadStep(steps As Integer)
        currentStep = steps

        HighlightStepper(steps)
        UpdateNavigationButtons()

        Select Case steps
            Case 1 : RenderUserControl(New UcManageImports())
            Case 2 : RenderUserControl(New UcManageEmployee())
            Case 3 : RenderUserControl(New UcManageEmployee())
            Case 4 : RenderUserControl(New UcManageEmployee())
            Case 5 : RenderUserControl(New UcPayrollStepOne())
            Case 6 : RenderUserControl(New UcPayrollStepTwo())
            Case 7 : RenderUserControl(New UcPayrollStepThree())
            Case 8 : RenderUserControl(New UcPayrollStepFour())
        End Select
    End Sub

    Private Sub HighlightStepper(activeStep As Integer)
        For Each pnl As Panel In tblpStepper.Controls.OfType(Of Panel)()
            Dim index As Integer = CInt(pnl.Tag)
            Dim pic = pnl.Controls.OfType(Of PictureBox)().First()

            If index < activeStep Then
                pic.Image = My.Resources.round_success_32   ' completed
            ElseIf index = activeStep Then
                pic.Image = My.Resources.round_processing_32   ' active
            Else
                pic.Image = My.Resources.round_pending_32 ' pending
            End If
        Next
    End Sub

    Private Sub UpdateNavigationButtons()
        btnBack.Enabled = (currentStep > 1)
        btnNext.Enabled = (currentStep < TOTAL_STEPS)

        If currentStep = TOTAL_STEPS Then
            btnNext.Text = "Finish"
        Else
            btnNext.Text = "Next"
        End If
    End Sub

    Private Function CanProceed(steps As Integer) As Boolean
        Select Case steps
            Case 1
                Return CType(pnlUserControlDisplay.Controls(0), UcManageImports).IsValid()
            Case 2
                Return CType(pnlUserControlDisplay.Controls(0), UcManageEmployee).IsValid()
            Case Else
                Return True
        End Select
    End Function

    Private Sub btnSet_Click(sender As Object, e As EventArgs) Handles btnSet.Click

    End Sub

    Private Sub btnMore_Click(sender As Object, e As EventArgs) Handles btnMore.Click
        Dim obj As New FrmSettings
        obj.ShowDialog()
    End Sub

    Private Sub btnMaximize_Click(sender As Object, e As EventArgs) Handles btnMaximize.Click
        Me.Close()
    End Sub

    Private Sub btnMinimize_Click(sender As Object, e As EventArgs) Handles btnMinimize.Click
        Me.WindowState = FormWindowState.Minimized
    End Sub
End Class