<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class FrmShowEmployeeReceivable
    Inherits System.Windows.Forms.Form

    'Form overrides dispose to clean up the component list.
    <System.Diagnostics.DebuggerNonUserCode()> _
    Protected Overrides Sub Dispose(ByVal disposing As Boolean)
        Try
            If disposing AndAlso components IsNot Nothing Then
                components.Dispose()
            End If
        Finally
            MyBase.Dispose(disposing)
        End Try
    End Sub

    'Required by the Windows Form Designer
    Private components As System.ComponentModel.IContainer

    'NOTE: The following procedure is required by the Windows Form Designer
    'It can be modified using the Windows Form Designer.  
    'Do not modify it using the code editor.
    <System.Diagnostics.DebuggerStepThrough()> _
    Private Sub InitializeComponent()
        Dim DataGridViewCellStyle1 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle4 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle2 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle3 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim resources As System.ComponentModel.ComponentResourceManager = New System.ComponentModel.ComponentResourceManager(GetType(FrmShowEmployeeReceivable))
        Me.panelContainer = New System.Windows.Forms.Panel()
        Me.dgvTable = New System.Windows.Forms.DataGridView()
        Me.number = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_receivable_id = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.payroll_period_id = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_id = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.receivable_id = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.receivable_name = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.receivable_code = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.deduction_amount = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.receivable_completed_on = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.receivable_terms_paid = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.receivable_billing_cycle = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.receivable_reason = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.receivable_is_default = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.receivable_status = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.action_edit = New System.Windows.Forms.DataGridViewButtonColumn()
        Me.action_stop = New System.Windows.Forms.DataGridViewButtonColumn()
        Me.panelTop = New System.Windows.Forms.Panel()
        Me.GroupBox1 = New System.Windows.Forms.GroupBox()
        Me.btnAdd = New System.Windows.Forms.Button()
        Me.lblEmployeeName = New System.Windows.Forms.Label()
        Me.panelNav = New System.Windows.Forms.Panel()
        Me.btnMinimize = New System.Windows.Forms.Button()
        Me.btnClose = New System.Windows.Forms.Button()
        Me.panelContainer.SuspendLayout()
        CType(Me.dgvTable, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.panelTop.SuspendLayout()
        Me.GroupBox1.SuspendLayout()
        Me.panelNav.SuspendLayout()
        Me.SuspendLayout()
        '
        'panelContainer
        '
        Me.panelContainer.BackColor = System.Drawing.Color.WhiteSmoke
        Me.panelContainer.Controls.Add(Me.dgvTable)
        Me.panelContainer.Controls.Add(Me.panelTop)
        Me.panelContainer.Controls.Add(Me.panelNav)
        Me.panelContainer.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContainer.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Pixel)
        Me.panelContainer.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        Me.panelContainer.Location = New System.Drawing.Point(0, 0)
        Me.panelContainer.Name = "panelContainer"
        Me.panelContainer.Size = New System.Drawing.Size(998, 586)
        Me.panelContainer.TabIndex = 8
        '
        'dgvTable
        '
        Me.dgvTable.AllowUserToAddRows = False
        Me.dgvTable.AllowUserToDeleteRows = False
        Me.dgvTable.AutoSizeColumnsMode = System.Windows.Forms.DataGridViewAutoSizeColumnsMode.Fill
        Me.dgvTable.AutoSizeRowsMode = System.Windows.Forms.DataGridViewAutoSizeRowsMode.AllCellsExceptHeaders
        Me.dgvTable.BackgroundColor = System.Drawing.Color.White
        Me.dgvTable.BorderStyle = System.Windows.Forms.BorderStyle.None
        Me.dgvTable.ColumnHeadersBorderStyle = System.Windows.Forms.DataGridViewHeaderBorderStyle.None
        DataGridViewCellStyle1.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleLeft
        DataGridViewCellStyle1.BackColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        DataGridViewCellStyle1.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Pixel)
        DataGridViewCellStyle1.ForeColor = System.Drawing.Color.White
        DataGridViewCellStyle1.Padding = New System.Windows.Forms.Padding(10)
        DataGridViewCellStyle1.SelectionBackColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        DataGridViewCellStyle1.SelectionForeColor = System.Drawing.Color.White
        DataGridViewCellStyle1.WrapMode = System.Windows.Forms.DataGridViewTriState.[True]
        Me.dgvTable.ColumnHeadersDefaultCellStyle = DataGridViewCellStyle1
        Me.dgvTable.ColumnHeadersHeightSizeMode = System.Windows.Forms.DataGridViewColumnHeadersHeightSizeMode.AutoSize
        Me.dgvTable.Columns.AddRange(New System.Windows.Forms.DataGridViewColumn() {Me.number, Me.employee_receivable_id, Me.payroll_period_id, Me.employee_id, Me.receivable_id, Me.receivable_name, Me.receivable_code, Me.deduction_amount, Me.receivable_completed_on, Me.receivable_terms_paid, Me.receivable_billing_cycle, Me.receivable_reason, Me.receivable_is_default, Me.receivable_status, Me.action_edit, Me.action_stop})
        DataGridViewCellStyle4.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleLeft
        DataGridViewCellStyle4.BackColor = System.Drawing.SystemColors.Window
        DataGridViewCellStyle4.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Pixel)
        DataGridViewCellStyle4.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        DataGridViewCellStyle4.Padding = New System.Windows.Forms.Padding(5)
        DataGridViewCellStyle4.SelectionBackColor = System.Drawing.SystemColors.GradientInactiveCaption
        DataGridViewCellStyle4.SelectionForeColor = System.Drawing.SystemColors.Desktop
        DataGridViewCellStyle4.WrapMode = System.Windows.Forms.DataGridViewTriState.[False]
        Me.dgvTable.DefaultCellStyle = DataGridViewCellStyle4
        Me.dgvTable.Dock = System.Windows.Forms.DockStyle.Fill
        Me.dgvTable.EditMode = System.Windows.Forms.DataGridViewEditMode.EditProgrammatically
        Me.dgvTable.EnableHeadersVisualStyles = False
        Me.dgvTable.Location = New System.Drawing.Point(0, 100)
        Me.dgvTable.Name = "dgvTable"
        Me.dgvTable.RowHeadersBorderStyle = System.Windows.Forms.DataGridViewHeaderBorderStyle.None
        Me.dgvTable.RowHeadersVisible = False
        Me.dgvTable.SelectionMode = System.Windows.Forms.DataGridViewSelectionMode.FullRowSelect
        Me.dgvTable.Size = New System.Drawing.Size(998, 486)
        Me.dgvTable.TabIndex = 12
        '
        'number
        '
        Me.number.FillWeight = 40.0!
        Me.number.HeaderText = "#"
        Me.number.Name = "number"
        '
        'employee_receivable_id
        '
        Me.employee_receivable_id.HeaderText = "ID"
        Me.employee_receivable_id.Name = "employee_receivable_id"
        Me.employee_receivable_id.Visible = False
        '
        'payroll_period_id
        '
        Me.payroll_period_id.HeaderText = "Payroll Period ID"
        Me.payroll_period_id.Name = "payroll_period_id"
        Me.payroll_period_id.Visible = False
        '
        'employee_id
        '
        Me.employee_id.HeaderText = "Employee ID"
        Me.employee_id.Name = "employee_id"
        Me.employee_id.Visible = False
        '
        'receivable_id
        '
        Me.receivable_id.HeaderText = "Receivable ID"
        Me.receivable_id.Name = "receivable_id"
        Me.receivable_id.Visible = False
        '
        'receivable_name
        '
        Me.receivable_name.HeaderText = "Name"
        Me.receivable_name.Name = "receivable_name"
        '
        'receivable_code
        '
        Me.receivable_code.HeaderText = "Code"
        Me.receivable_code.Name = "receivable_code"
        '
        'deduction_amount
        '
        Me.deduction_amount.HeaderText = "Amount"
        Me.deduction_amount.Name = "deduction_amount"
        '
        'receivable_completed_on
        '
        Me.receivable_completed_on.HeaderText = "Completed On"
        Me.receivable_completed_on.Name = "receivable_completed_on"
        Me.receivable_completed_on.Visible = False
        '
        'receivable_terms_paid
        '
        Me.receivable_terms_paid.HeaderText = "Terms Paid"
        Me.receivable_terms_paid.Name = "receivable_terms_paid"
        '
        'receivable_billing_cycle
        '
        Me.receivable_billing_cycle.HeaderText = "Billing Cycle"
        Me.receivable_billing_cycle.Name = "receivable_billing_cycle"
        '
        'receivable_reason
        '
        Me.receivable_reason.HeaderText = "Remarks"
        Me.receivable_reason.Name = "receivable_reason"
        '
        'receivable_is_default
        '
        Me.receivable_is_default.HeaderText = "Is Default"
        Me.receivable_is_default.Name = "receivable_is_default"
        Me.receivable_is_default.Visible = False
        '
        'receivable_status
        '
        Me.receivable_status.HeaderText = "Status"
        Me.receivable_status.Name = "receivable_status"
        '
        'action_edit
        '
        DataGridViewCellStyle2.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleCenter
        DataGridViewCellStyle2.ForeColor = System.Drawing.Color.SteelBlue
        DataGridViewCellStyle2.SelectionForeColor = System.Drawing.Color.SteelBlue
        Me.action_edit.DefaultCellStyle = DataGridViewCellStyle2
        Me.action_edit.FillWeight = 50.0!
        Me.action_edit.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.action_edit.HeaderText = ""
        Me.action_edit.Name = "action_edit"
        '
        'action_stop
        '
        DataGridViewCellStyle3.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleCenter
        DataGridViewCellStyle3.ForeColor = System.Drawing.Color.IndianRed
        DataGridViewCellStyle3.SelectionForeColor = System.Drawing.Color.IndianRed
        Me.action_stop.DefaultCellStyle = DataGridViewCellStyle3
        Me.action_stop.FillWeight = 50.0!
        Me.action_stop.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.action_stop.HeaderText = ""
        Me.action_stop.Name = "action_stop"
        '
        'panelTop
        '
        Me.panelTop.Controls.Add(Me.GroupBox1)
        Me.panelTop.Dock = System.Windows.Forms.DockStyle.Top
        Me.panelTop.Location = New System.Drawing.Point(0, 31)
        Me.panelTop.Name = "panelTop"
        Me.panelTop.Size = New System.Drawing.Size(998, 69)
        Me.panelTop.TabIndex = 2
        '
        'GroupBox1
        '
        Me.GroupBox1.Anchor = CType((((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom) _
            Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.GroupBox1.Controls.Add(Me.btnAdd)
        Me.GroupBox1.Controls.Add(Me.lblEmployeeName)
        Me.GroupBox1.Location = New System.Drawing.Point(12, 6)
        Me.GroupBox1.Name = "GroupBox1"
        Me.GroupBox1.Size = New System.Drawing.Size(974, 57)
        Me.GroupBox1.TabIndex = 0
        Me.GroupBox1.TabStop = False
        '
        'btnAdd
        '
        Me.btnAdd.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnAdd.BackColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.btnAdd.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnAdd.Font = New System.Drawing.Font("Segoe UI Semibold", 9.75!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnAdd.ForeColor = System.Drawing.Color.White
        Me.btnAdd.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnAdd.Location = New System.Drawing.Point(830, 14)
        Me.btnAdd.Name = "btnAdd"
        Me.btnAdd.Size = New System.Drawing.Size(138, 36)
        Me.btnAdd.TabIndex = 25
        Me.btnAdd.Text = "ADD"
        Me.btnAdd.TextImageRelation = System.Windows.Forms.TextImageRelation.ImageBeforeText
        Me.btnAdd.UseVisualStyleBackColor = False
        '
        'lblEmployeeName
        '
        Me.lblEmployeeName.AutoSize = True
        Me.lblEmployeeName.Font = New System.Drawing.Font("Segoe UI", 18.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblEmployeeName.Location = New System.Drawing.Point(6, 16)
        Me.lblEmployeeName.Name = "lblEmployeeName"
        Me.lblEmployeeName.Size = New System.Drawing.Size(204, 32)
        Me.lblEmployeeName.TabIndex = 0
        Me.lblEmployeeName.Text = "Admin A. Admin"
        '
        'panelNav
        '
        Me.panelNav.BackColor = System.Drawing.Color.FromArgb(CType(CType(10, Byte), Integer), CType(CType(62, Byte), Integer), CType(CType(48, Byte), Integer))
        Me.panelNav.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle
        Me.panelNav.Controls.Add(Me.btnMinimize)
        Me.panelNav.Controls.Add(Me.btnClose)
        Me.panelNav.Dock = System.Windows.Forms.DockStyle.Top
        Me.panelNav.Location = New System.Drawing.Point(0, 0)
        Me.panelNav.Name = "panelNav"
        Me.panelNav.Size = New System.Drawing.Size(998, 31)
        Me.panelNav.TabIndex = 1
        '
        'btnMinimize
        '
        Me.btnMinimize.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnMinimize.BackColor = System.Drawing.Color.Transparent
        Me.btnMinimize.BackgroundImage = CType(resources.GetObject("btnMinimize.BackgroundImage"), System.Drawing.Image)
        Me.btnMinimize.BackgroundImageLayout = System.Windows.Forms.ImageLayout.Center
        Me.btnMinimize.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnMinimize.ForeColor = System.Drawing.Color.White
        Me.btnMinimize.Location = New System.Drawing.Point(913, 3)
        Me.btnMinimize.Name = "btnMinimize"
        Me.btnMinimize.Size = New System.Drawing.Size(37, 22)
        Me.btnMinimize.TabIndex = 1
        Me.btnMinimize.UseVisualStyleBackColor = False
        '
        'btnClose
        '
        Me.btnClose.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnClose.BackColor = System.Drawing.Color.Red
        Me.btnClose.BackgroundImage = Global.zcmc_payroll_client_v2.My.Resources.Resources.close_16
        Me.btnClose.BackgroundImageLayout = System.Windows.Forms.ImageLayout.Center
        Me.btnClose.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnClose.ForeColor = System.Drawing.Color.Red
        Me.btnClose.Location = New System.Drawing.Point(956, 3)
        Me.btnClose.Name = "btnClose"
        Me.btnClose.Size = New System.Drawing.Size(37, 22)
        Me.btnClose.TabIndex = 0
        Me.btnClose.UseVisualStyleBackColor = False
        '
        'FrmShowEmployeeReceivable
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.ClientSize = New System.Drawing.Size(998, 586)
        Me.Controls.Add(Me.panelContainer)
        Me.FormBorderStyle = System.Windows.Forms.FormBorderStyle.None
        Me.Name = "FrmShowEmployeeReceivable"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterParent
        Me.Text = "FrmShowEmployeeReceivable"
        Me.WindowState = System.Windows.Forms.FormWindowState.Maximized
        Me.panelContainer.ResumeLayout(False)
        CType(Me.dgvTable, System.ComponentModel.ISupportInitialize).EndInit()
        Me.panelTop.ResumeLayout(False)
        Me.GroupBox1.ResumeLayout(False)
        Me.GroupBox1.PerformLayout()
        Me.panelNav.ResumeLayout(False)
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents panelContainer As Panel
    Friend WithEvents panelTop As Panel
    Friend WithEvents GroupBox1 As GroupBox
    Friend WithEvents btnAdd As Button
    Friend WithEvents lblEmployeeName As Label
    Friend WithEvents panelNav As Panel
    Friend WithEvents btnMinimize As Button
    Friend WithEvents btnClose As Button
    Friend WithEvents dgvTable As DataGridView
    Friend WithEvents number As DataGridViewTextBoxColumn
    Friend WithEvents employee_receivable_id As DataGridViewTextBoxColumn
    Friend WithEvents payroll_period_id As DataGridViewTextBoxColumn
    Friend WithEvents employee_id As DataGridViewTextBoxColumn
    Friend WithEvents receivable_id As DataGridViewTextBoxColumn
    Friend WithEvents receivable_name As DataGridViewTextBoxColumn
    Friend WithEvents receivable_code As DataGridViewTextBoxColumn
    Friend WithEvents deduction_amount As DataGridViewTextBoxColumn
    Friend WithEvents receivable_completed_on As DataGridViewTextBoxColumn
    Friend WithEvents receivable_terms_paid As DataGridViewTextBoxColumn
    Friend WithEvents receivable_billing_cycle As DataGridViewTextBoxColumn
    Friend WithEvents receivable_reason As DataGridViewTextBoxColumn
    Friend WithEvents receivable_is_default As DataGridViewTextBoxColumn
    Friend WithEvents receivable_status As DataGridViewTextBoxColumn
    Friend WithEvents action_edit As DataGridViewButtonColumn
    Friend WithEvents action_stop As DataGridViewButtonColumn
End Class
