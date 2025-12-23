Public Class UcManageEmployee

    ' ===== Animation settings =====
    Private Const PANEL_WIDTH As Integer = 350
    Private Const ANIMATION_STEP As Integer = 60

    Private isExpanding As Boolean = False
    Private isCollapsing As Boolean = False

    Private WithEvents animationTimer As New Timer With {
        .Interval = 10
    }

    Public Function IsValid() As Boolean
        ' validate step logic
        Return True
    End Function

    Private Sub UcManageEmployee_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        SplitContainer.Panel2Collapsed = True

        dgvTable.Rows.Add("1", "1", "EMP-001", "Juan Dela Cruz", "Admin Aide III", "HRMO", Nothing, "Included", "View", "Exclude", "Manage Deductions", "Manage Receivables")
        dgvTable.Rows.Add("2", "2", "EMP-002", "Maria Santos", "Accounting I", "Finance", Nothing, "Included", "View", "Exclude", "Manage Deductions", "Manage Receivables")
        dgvTable.Rows.Add("3", "3", "EMP-003", "Pedro Reyes", "Computer Programmer I", "IISU", Nothing, "Included", "View", "Exclude", "Manage Deductions", "Manage Receivables")
    End Sub

    Private Sub btnIncludedEmployee_Click(sender As Object, e As EventArgs) Handles btnIncludedEmployee.Click
        btnIncludedEmployee.BackColor = Color.FromArgb(15, 87, 33)
        btnIncludedEmployee.ForeColor = Color.White

        btnExcludedEmployee.ForeColor = Color.Black
        btnExcludedEmployee.BackColor = Color.LightGray
    End Sub

    Private Sub btnExcludedEmployee_Click(sender As Object, e As EventArgs) Handles btnExcludedEmployee.Click
        btnIncludedEmployee.BackColor = Color.LightGray
        btnIncludedEmployee.ForeColor = Color.Black

        btnExcludedEmployee.ForeColor = Color.White
        btnExcludedEmployee.BackColor = Color.FromArgb(15, 87, 33)
    End Sub

    Private Sub dgvView_CellContentClick(sender As Object, e As DataGridViewCellEventArgs) Handles dgvTable.CellContentClick
        If e.RowIndex < 0 Then Exit Sub

        If e.ColumnIndex = 8 Then
            ' Load employee info
            Dim row = dgvTable.SelectedRows(0)
            lblName.Text = row.Cells(1).Value
            lblDesignation.Text = row.Cells(2).Value

            ' Show panel with animation
            ShowEmployeeInfo()
        ElseIf e.ColumnIndex = 10 Then
            Dim obj As New FrmShowEmployeeDeduction
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
