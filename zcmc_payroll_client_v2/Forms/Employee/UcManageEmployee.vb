Public Class UcManageEmployee
    Private service As New EmployeeService

    Public isManageDeduction As Boolean = False
    Public isManageReceivable As Boolean = False
    Public isManageBoth As Boolean = False

    ' ===== Animation settings =====
    Private Const PANEL_WIDTH As Integer = 350
    Private Const ANIMATION_STEP As Integer = 450

    Private isExpanding As Boolean = False
    Private isCollapsing As Boolean = False

    Private WithEvents animationTimer As New Timer With {
        .Interval = 10
    }

    Public Function IsValid() As Boolean
        ' validate step logic
        Return True
    End Function

    Private Async Sub UcManageEmployee_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        If isManageDeduction Then
            dgvTable.Columns(11).Visible = True
            dgvTable.Columns(11).Visible = False
        End If

        If isManageReceivable Then
            dgvTable.Columns(10).Visible = False
            dgvTable.Columns(11).Visible = True
        End If

        If isManageBoth Then
            dgvTable.Columns(10).Visible = True
            dgvTable.Columns(11).Visible = True
        End If


        SplitContainer.Panel2Collapsed = True


        'Await LoadingHelper.RunAsync(
        '    Async Function()
        '        Await service.GetEmployee(dgvTable, False)
        '    End Function,
        '    True
        ')
    End Sub

    Private Async Sub btnIncludedEmployee_Click(sender As Object, e As EventArgs) Handles btnIncludedEmployee.Click
        Await LoadingHelper.RunAsync(
            Async Function()
                Await service.GetEmployee(dgvTable, "isIncluded", False)
            End Function,
            True
        )

        HideEmployeeInfo()

        btnIncludedEmployee.BackColor = Color.FromArgb(15, 87, 33)
        btnIncludedEmployee.ForeColor = Color.White

        btnExcludedEmployee.ForeColor = Color.Black
        btnExcludedEmployee.BackColor = Color.LightGray
    End Sub

    Private Async Sub btnExcludedEmployee_Click(sender As Object, e As EventArgs) Handles btnExcludedEmployee.Click
        Await LoadingHelper.RunAsync(
            Async Function()
                Await service.GetEmployee(dgvTable, "isExcluded", False)
            End Function,
            True
        )

        HideEmployeeInfo()

        btnIncludedEmployee.BackColor = Color.LightGray
        btnIncludedEmployee.ForeColor = Color.Black

        btnExcludedEmployee.ForeColor = Color.White
        btnExcludedEmployee.BackColor = Color.FromArgb(15, 87, 33)
    End Sub

    Private Async Sub dgvView_CellContentClick(sender As Object, e As DataGridViewCellEventArgs) Handles dgvTable.CellContentClick
        If e.RowIndex < 0 Then Exit Sub

        Dim row = dgvTable.SelectedRows(0)

        If e.ColumnIndex = 8 Then
            ' Show panel with animation
            ShowEmployeeInfo()

            ' Load employee info
            lblEmployeeID.Text = row.Cells(2).Value
            lblName.Text = row.Cells(3).Value
            lblDesignation.Text = row.Cells(4).Value
            lblAssignedArea.Text = row.Cells(5).Value

            If isManageDeduction = True Then
                Await LoadingHelper.RunAsync(
                    Async Function()
                        Await service.GetEmployeeDeductionList(dgvList, row.Cells(1).Value)
                    End Function,
                    True
                )
            Else


            End If

        ElseIf e.ColumnIndex = 10 Then
            Dim obj As New FrmShowEmployeeDeduction
            obj.lblEmployeeName.Text = row.Cells(3).Value
            obj._employeeID = row.Cells(1).Value
            obj.ShowDialog()
        ElseIf e.ColumnIndex = 11 Then
            Dim obj As New FrmShowEmployeeReceivable
            obj.ShowDialog()
        End If
    End Sub

    Private Sub btnClose_Click(sender As Object, e As EventArgs) Handles btnClose.Click
        HideEmployeeInfo()
    End Sub

    Private Sub ShowEmployeeInfo()
        If SplitContainer.Panel2Collapsed Then
            SplitContainer.Panel2Collapsed = False
            SplitContainer.SplitterDistance = SplitContainer.Width
        End If

        isExpanding = True
        isCollapsing = False
        animationTimer.Start()
    End Sub

    Private Sub HideEmployeeInfo()
        If SplitContainer.Panel2Collapsed Then Exit Sub

        isExpanding = False
        isCollapsing = True
        animationTimer.Start()
    End Sub

    '===== ANIMATION TIMER =====
    Private Sub animationTimer_Tick(sender As Object, e As EventArgs) Handles animationTimer.Tick
        If isExpanding Then
            SplitContainer.SplitterDistance -= ANIMATION_STEP

            If SplitContainer.Width - SplitContainer.SplitterDistance >= PANEL_WIDTH Then
                SplitContainer.SplitterDistance = SplitContainer.Width - PANEL_WIDTH
                animationTimer.Stop()
                isExpanding = False
            End If

        ElseIf isCollapsing Then
            SplitContainer.SplitterDistance += ANIMATION_STEP

            If SplitContainer.SplitterDistance >= SplitContainer.Width Then
                animationTimer.Stop()
                isCollapsing = False

                ' 🔴 COLLAPSE ONLY AFTER ANIMATION FINISHES
                SplitContainer.Panel2Collapsed = True
            End If
        End If
    End Sub
End Class
