Public Class UcManageEmployee
    Dim helper As New Helpers

    Private service As New EmployeeService
    Private preview As New EmployeePreviewService

    Public Property Pagination As New PaginationContext()

    Public isManageDeduction As Boolean = False
    Public isManageReceivable As Boolean = False
    Public isManageBoth As Boolean = False

    Public isInclude As Boolean = False
    Public isExclude As Boolean = False

    Dim perPage As Integer = 10
    Dim page As Integer = 1

    ' ===== Animation settings =====
    Private Const PANEL_WIDTH As Integer = 350
    Private Const ANIMATION_STEP As Integer = 450

    Private isExpanding As Boolean = False
    Private isCollapsing As Boolean = False

    Private WithEvents animationTimer As New Timer With {
        .Interval = 10
    }

    Public Function IsValid() As Boolean
        ' Only validate when in Excluded step (manage both)
        If isManageBoth Then

            Dim result = MessageBox.Show(
                "You are about to proceed to payroll setup and generation. Have you completed managing all deductions and receivables?",
                "Confirm Action",
                MessageBoxButtons.YesNo,
                MessageBoxIcon.Question
            )

            If result = DialogResult.No Then
                Return False
            End If

        End If

        Return True
    End Function

    Private Async Sub UcManageEmployee_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        cmbPerPage.SelectedIndex = 0

        Pagination.PerPage = CInt(cmbPerPage.SelectedItem)
        Pagination.Page = 1

        If isManageDeduction Then
            lblTitle.Text = "Manage Employee Deductions"
            dgvTable.Columns(11).Visible = True
            dgvTable.Columns(11).Visible = False
            btnIncludedEmployee.PerformClick()
        End If

        If isManageReceivable Then
            lblTitle.Text = "Manage Employee Receivables"
            dgvTable.Columns(10).Visible = False
            dgvTable.Columns(11).Visible = True
            btnIncludedEmployee.PerformClick()
        End If

        If isManageBoth Then
            lblTitle.Text = "Manage Excluded Employee"
            dgvTable.Columns(8).Visible = False
            dgvTable.Columns(9).Visible = False
            dgvTable.Columns(10).Visible = True
            dgvTable.Columns(11).Visible = True

            btnIncludedEmployee.Enabled = False
            btnExcludedEmployee.Enabled = False

            'Await LoadingHelper.RunAsync(
            '    Async Function()
            '        Await adjustmentService.GetEmployeeAdjustment(dgvTable, "isExcluded", False)
            '    End Function,
            '    True
            ')
        End If

        helper.CheckDgvRows(dgvTable, lblMessage)

        SplitContainer.Panel2Collapsed = True
    End Sub

    Private Async Sub btnIncludedEmployee_Click(sender As Object, e As EventArgs) Handles btnIncludedEmployee.Click
        isInclude = True
        isExclude = False

        Pagination.Page = 1
        Await LoadEmployees()

        HideEmployeeInfo()
        helper.CheckDgvRows(dgvTable, lblMessage)

        btnIncludedEmployee.BackColor = Color.FromArgb(15, 87, 33)
        btnIncludedEmployee.ForeColor = Color.White

        btnExcludedEmployee.ForeColor = Color.Black
        btnExcludedEmployee.BackColor = Color.LightGray
    End Sub

    Private Async Sub btnExcludedEmployee_Click(sender As Object, e As EventArgs) Handles btnExcludedEmployee.Click
        isInclude = False
        isExclude = True

        Pagination.Page = 1
        Await LoadEmployees()

        HideEmployeeInfo()
        helper.CheckDgvRows(dgvTable, lblMessage)

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
            ElseIf isManageReceivable = True Then
                Await LoadingHelper.RunAsync(
                    Async Function()
                        Await service.GetEmployeeReceivableList(dgvList, row.Cells(1).Value)
                    End Function,
                    True
                )
            End If
        ElseIf e.ColumnIndex = 9 Then
            If isInclude = True Then
                Dim res = MessageBox.Show("Are you sure you want to exclude this employee: " & row.Cells(3).Value & "?", "Confirm Action", MessageBoxButtons.YesNo, MessageBoxIcon.Question)
                If res = DialogResult.Yes Then
                    Using obj As New FrmAuthorizationPin
                        If obj.ShowDialog() = DialogResult.OK Then
                            ' Do exclude logic here
                        End If
                    End Using
                End If

            ElseIf isExclude = True Then
                Dim res = MessageBox.Show("Are you sure you want to include this employee: " & row.Cells(3).Value & "?", "Confirm Action", MessageBoxButtons.YesNo, MessageBoxIcon.Question)
                If res = DialogResult.Yes Then
                    Using obj As New FrmAuthorizationPin
                        If obj.ShowDialog() = DialogResult.OK Then
                            ' Do include logic here
                        End If
                    End Using
                End If
            End If
        ElseIf e.ColumnIndex = 10 Then
                Dim obj As New FrmShowEmployeeDeduction
                obj.lblEmployeeName.Text = row.Cells(3).Value
                obj._employeeID = row.Cells(1).Value
                obj.ShowDialog()
            ElseIf e.ColumnIndex = 11 Then
                Dim obj As New FrmShowEmployeeReceivable
            obj.lblEmployeeName.Text = row.Cells(3).Value
            obj._employeeID = row.Cells(1).Value
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

    Private Async Sub cmbPerPage_SelectedIndexChanged(sender As Object, e As EventArgs) Handles cmbPerPage.SelectedIndexChanged
        If cmbPerPage.SelectedItem Is Nothing Then Exit Sub

        Pagination.PerPage = CInt(cmbPerPage.SelectedItem)
        Pagination.Page = 1

        Await LoadEmployees()
    End Sub

    Private Async Sub cmbPage_SelectedIndexChanged(sender As Object, e As EventArgs) Handles cmbPage.SelectedIndexChanged
        If cmbPage.SelectedItem Is Nothing Then Exit Sub

        Pagination.Page = CInt(cmbPage.SelectedItem)
        Await LoadEmployees()
    End Sub


    Private Async Sub btnPrevious_Click(sender As Object, e As EventArgs) Handles btnPrevious.Click
        If Pagination.Page <= 1 Then Exit Sub

        Pagination.Page -= 1
        Await LoadEmployees()
    End Sub

    Private Async Sub btnNext_Click(sender As Object, e As EventArgs) Handles btnNext.Click
        If Pagination.Page >= Pagination.LastPage Then Exit Sub

        Pagination.Page += 1
        Await LoadEmployees()
    End Sub

    Private Async Function LoadEmployees() As Task
        Dim type As String = If(isInclude, "isIncluded", "isExcluded")

        If isManageBoth Then
            Await LoadingHelper.RunAsync(
                Async Function()
                    Await preview.GetExcluded(dgvTable, Nothing, Pagination)
                End Function,
                True
            )
        Else
            Await LoadingHelper.RunAsync(
                Async Function()
                    Await service.GetEmployee(dgvTable, type, True, Pagination)
                End Function,
                True
            )
        End If

        helper.UpdatePaginationControls(Pagination, cmbPage, lblPerPage, lblPage, btnPrevious, btnNext, AddressOf cmbPage_SelectedIndexChanged)
        helper.CheckDgvRows(dgvTable, lblMessage)
    End Function
End Class
