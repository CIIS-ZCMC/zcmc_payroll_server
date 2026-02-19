Imports System.Linq.Expressions
Imports MaterialSkin

Public Class FrmStepper
    Private payrollPeriodService As New PayrollPeriodService()
    Private payrollProcessService As New PayrollProcessService()

    Private payrollContext As New PayrollWizardContext()

    Private currentStep As Integer = 1
    Private TOTAL_STEPS As Integer

    'Private Const TOTAL_STEPS As Integer = 8

    Private _session As PayrollSession
    Public Sub New(session As PayrollSession)
        InitializeComponent()
        _session = session
    End Sub

    Private Async Sub FrmStepper_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        'Await payrollPeriodService.GetActivePayroll(lblMonthYear, True)

        'Await LoadingHelper.RunAsync(
        '    Async Function()
        '        Await payrollPeriodService.GetActivePayroll(lblMonthYear, True)
        '    End Function,
        '    True
        ')

        'Me.Enabled = False

        'Dim frm As New FrmPayrollType
        'frm.ShowDialog()

        'currentStep = AppState.CurrentPayrollProcess.current_step

        'Me.Enabled = True

        lblMonthYear.Text = $"{_session.PayrollPeriod.month}/{_session.PayrollPeriod.year}"

        tblpStepper.Controls.Clear()

        Select Case _session.PayrollType
            Case PayrollTypes.regular, PayrollTypes.job_order
                TOTAL_STEPS = 8

                tblpStepper.Controls.Add(CreateStepper(1, "Import"), 0, 0)
                tblpStepper.Controls.Add(CreateStepper(2, "Deductions"), 1, 0)
                tblpStepper.Controls.Add(CreateStepper(3, "Receivables"), 2, 0)
                tblpStepper.Controls.Add(CreateStepper(4, "Excluded"), 3, 0)
                tblpStepper.Controls.Add(CreateStepper(5, "Set"), 4, 0)
                tblpStepper.Controls.Add(CreateStepper(6, "Select"), 5, 0)
                tblpStepper.Controls.Add(CreateStepper(7, "Review"), 6, 0)
                tblpStepper.Controls.Add(CreateStepper(8, "View"), 7, 0)

            Case PayrollTypes.night_differential
                TOTAL_STEPS = 4

                tblpStepper.Controls.Add(CreateStepper(1, "Set"), 0, 0)
                tblpStepper.Controls.Add(CreateStepper(2, "Select"), 1, 0)
                tblpStepper.Controls.Add(CreateStepper(3, "Review"), 2, 0)
                tblpStepper.Controls.Add(CreateStepper(4, "View"), 3, 0)
        End Select

        currentStep = _session.CurrentPayrollProcess.current_step

        If currentStep < 1 OrElse currentStep > TOTAL_STEPS Then
            currentStep = 1
        End If

        LoadStep(currentStep) ' default
    End Sub

    Private Async Sub btnNext_Click(sender As Object, e As EventArgs) Handles btnNext.Click
        'If currentStep < TOTAL_STEPS Then
        '    If Not Await CanProceed(currentStep) Then Exit Sub

        '    Dim nextStep = currentStep + 1
        '    Await payrollProcessService.UpdateProcess(_session.CurrentPayrollProcess.id, nextStep, processStatus)
        '    _session.CurrentPayrollProcess.current_step = nextStep

        '    LoadStep(nextStep)
        '    'LoadStep(currentStep + 1)
        'End If

        If currentStep = TOTAL_STEPS Then
            Await payrollProcessService.UpdateProcess(_session.CurrentPayrollProcess.id, currentStep, PayrollProcessStatus.complete.ToString())

            MessageBox.Show("Payroll Completed Successfully.")
            Me.Close()
            Exit Sub
        End If

        If Not Await CanProceed(currentStep) Then Exit Sub

        Dim nextStep = currentStep + 1
        Dim result = Await payrollProcessService.UpdateProcess(_session.CurrentPayrollProcess.id, nextStep, PayrollProcessStatus.in_progress.ToString())

        If Not result.Success Then
            MessageBox.Show(result.Message)
            Exit Sub
        End If

        _session.CurrentPayrollProcess.current_step = nextStep
        LoadStep(nextStep)
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

        Select Case _session.PayrollType
            Case PayrollTypes.regular, PayrollTypes.job_order
                LoadRegularSteps(steps)

            Case PayrollTypes.night_differential
                LoadNightDiffSteps(steps)
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

    Private Function CanProceed(steps As Integer) As Task(Of Boolean)
        If _session.PayrollType = PayrollTypes.regular OrElse _session.PayrollType = PayrollTypes.job_order Then
            Select Case steps
                Case 1
                    Return CType(pnlUserControlDisplay.Controls(0), UcManageImports).IsValid()
                Case 2
                    Return CType(pnlUserControlDisplay.Controls(0), UcManageEmployee).IsValid()
                Case 4
                    Return CType(pnlUserControlDisplay.Controls(0), UcManageEmployee).IsValid()
                Case 5
                    Return CType(pnlUserControlDisplay.Controls(0), UcPayrollStepOne).IsValid()
                Case 6
                    Return CType(pnlUserControlDisplay.Controls(0), UcPayrollStepTwo).IsValid()
                Case 7
                    Return CType(pnlUserControlDisplay.Controls(0), UcPayrollStepThree).IsValid()
                Case Else
                    Return Task.FromResult(True)
            End Select

        ElseIf _session.PayrollType = PayrollTypes.night_differential Then
            Select Case steps
                Case 1
                    Return CType(pnlUserControlDisplay.Controls(0), UcPayrollStepOne).IsValid()
                Case 2
                    Return CType(pnlUserControlDisplay.Controls(0), UcPayrollStepOne).IsValid()
                Case 3
                    Return CType(pnlUserControlDisplay.Controls(0), UcPayrollStepOne).IsValid()
                Case Else
                    Return Task.FromResult(True)
            End Select
        End If

    End Function

    Private Sub LoadRegularSteps(steps As Integer)
        Select Case steps
            Case 1 : RenderUserControl(New UcManageImports())

            Case 2
                Dim uc As New UcManageEmployee()
                uc.isManageDeduction = True
                RenderUserControl(uc)

            Case 3
                Dim uc As New UcManageEmployee()
                uc.isManageReceivable = True
                RenderUserControl(uc)

            Case 4
                Dim uc As New UcManageEmployee()
                uc.isManageBoth = True
                RenderUserControl(uc)

            Case 5
                Dim uc As New UcPayrollStepOne()
                uc.Context = payrollContext
                RenderUserControl(uc)

            Case 6
                Dim uc As New UcPayrollStepTwo()
                uc.Context = payrollContext
                RenderUserControl(uc)

            Case 7
                Dim uc As New UcPayrollStepThree()
                uc.Context = payrollContext
                RenderUserControl(uc)
            Case 8 : RenderUserControl(New UcPayrollStepFour())
        End Select
    End Sub

    Private Sub LoadNightDiffSteps(steps As Integer)
        Select Case steps
            Case 1 : RenderUserControl(New UcPayrollStepOne)
            Case 2 : RenderUserControl(New UcPayrollStepOne)
            Case 3 : RenderUserControl(New UcPayrollStepOne)
            Case 4 : RenderUserControl(New UcPayrollStepOne)
        End Select
    End Sub

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