Imports MaterialSkin.Controls

<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()>
Partial Class FrmShowEmployeeDeduction
    Inherits System.Windows.Forms.Form

    'Form overrides dispose to clean up the component list.
    <System.Diagnostics.DebuggerNonUserCode()>
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
    <System.Diagnostics.DebuggerStepThrough()>
    Private Sub InitializeComponent()
        Dim DataGridViewCellStyle1 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle4 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle5 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle6 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim resources As System.ComponentModel.ComponentResourceManager = New System.ComponentModel.ComponentResourceManager(GetType(FrmShowEmployeeDeduction))
        Dim DataGridViewCellStyle2 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle3 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Me.panelContainer = New System.Windows.Forms.Panel()
        Me.dgvTable = New System.Windows.Forms.DataGridView()
        Me.panelTop = New System.Windows.Forms.Panel()
        Me.GroupBox1 = New System.Windows.Forms.GroupBox()
        Me.btnAdd = New System.Windows.Forms.Button()
        Me.lblEmployeeName = New System.Windows.Forms.Label()
        Me.panelNav = New System.Windows.Forms.Panel()
        Me.btnMinimize = New System.Windows.Forms.Button()
        Me.btnClose = New System.Windows.Forms.Button()
        Me.number = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_deduction_id = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.payroll_period_id = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_id = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.deduction_id = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.deduction_name = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.deduction_code = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.deduction_amount = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.percentage = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.completed_on = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.with_terms = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.total_term = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.terms_paid = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.billing_cycle = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.reason = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.is_default = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_deduction_status = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.action_edit = New System.Windows.Forms.DataGridViewButtonColumn()
        Me.action_stop = New System.Windows.Forms.DataGridViewButtonColumn()
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
        Me.panelContainer.TabIndex = 7
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
        Me.dgvTable.Columns.AddRange(New System.Windows.Forms.DataGridViewColumn() {Me.number, Me.employee_deduction_id, Me.payroll_period_id, Me.employee_id, Me.deduction_id, Me.deduction_name, Me.deduction_code, Me.deduction_amount, Me.percentage, Me.completed_on, Me.with_terms, Me.total_term, Me.terms_paid, Me.billing_cycle, Me.reason, Me.is_default, Me.employee_deduction_status, Me.action_edit, Me.action_stop})
        DataGridViewCellStyle4.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleLeft
        DataGridViewCellStyle4.BackColor = System.Drawing.Color.White
        DataGridViewCellStyle4.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Pixel)
        DataGridViewCellStyle4.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        DataGridViewCellStyle4.Padding = New System.Windows.Forms.Padding(5)
        DataGridViewCellStyle4.SelectionBackColor = System.Drawing.Color.Gainsboro
        DataGridViewCellStyle4.SelectionForeColor = System.Drawing.Color.Black
        DataGridViewCellStyle4.WrapMode = System.Windows.Forms.DataGridViewTriState.[False]
        Me.dgvTable.DefaultCellStyle = DataGridViewCellStyle4
        Me.dgvTable.Dock = System.Windows.Forms.DockStyle.Fill
        Me.dgvTable.EnableHeadersVisualStyles = False
        Me.dgvTable.Location = New System.Drawing.Point(0, 100)
        Me.dgvTable.MultiSelect = False
        Me.dgvTable.Name = "dgvTable"
        Me.dgvTable.ReadOnly = True
        Me.dgvTable.RowHeadersBorderStyle = System.Windows.Forms.DataGridViewHeaderBorderStyle.None
        DataGridViewCellStyle5.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleLeft
        DataGridViewCellStyle5.BackColor = System.Drawing.Color.White
        DataGridViewCellStyle5.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Pixel)
        DataGridViewCellStyle5.ForeColor = System.Drawing.Color.Black
        DataGridViewCellStyle5.SelectionBackColor = System.Drawing.Color.SteelBlue
        DataGridViewCellStyle5.SelectionForeColor = System.Drawing.SystemColors.HighlightText
        DataGridViewCellStyle5.WrapMode = System.Windows.Forms.DataGridViewTriState.[True]
        Me.dgvTable.RowHeadersDefaultCellStyle = DataGridViewCellStyle5
        Me.dgvTable.RowHeadersVisible = False
        DataGridViewCellStyle6.Font = New System.Drawing.Font("Tahoma", 11.25!)
        Me.dgvTable.RowsDefaultCellStyle = DataGridViewCellStyle6
        Me.dgvTable.SelectionMode = System.Windows.Forms.DataGridViewSelectionMode.FullRowSelect
        Me.dgvTable.Size = New System.Drawing.Size(998, 486)
        Me.dgvTable.TabIndex = 10
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
        'number
        '
        Me.number.FillWeight = 40.0!
        Me.number.HeaderText = "#"
        Me.number.Name = "number"
        Me.number.ReadOnly = True
        '
        'employee_deduction_id
        '
        Me.employee_deduction_id.HeaderText = "ID"
        Me.employee_deduction_id.Name = "employee_deduction_id"
        Me.employee_deduction_id.ReadOnly = True
        Me.employee_deduction_id.Visible = False
        '
        'payroll_period_id
        '
        Me.payroll_period_id.HeaderText = "Payroll Period ID"
        Me.payroll_period_id.Name = "payroll_period_id"
        Me.payroll_period_id.ReadOnly = True
        Me.payroll_period_id.Visible = False
        '
        'employee_id
        '
        Me.employee_id.HeaderText = "Employee ID"
        Me.employee_id.Name = "employee_id"
        Me.employee_id.ReadOnly = True
        Me.employee_id.Visible = False
        '
        'deduction_id
        '
        Me.deduction_id.HeaderText = "Deduction ID"
        Me.deduction_id.Name = "deduction_id"
        Me.deduction_id.ReadOnly = True
        Me.deduction_id.Visible = False
        '
        'deduction_name
        '
        Me.deduction_name.HeaderText = "Name"
        Me.deduction_name.Name = "deduction_name"
        Me.deduction_name.ReadOnly = True
        '
        'deduction_code
        '
        Me.deduction_code.HeaderText = "Code"
        Me.deduction_code.Name = "deduction_code"
        Me.deduction_code.ReadOnly = True
        '
        'deduction_amount
        '
        Me.deduction_amount.HeaderText = "Amount"
        Me.deduction_amount.Name = "deduction_amount"
        Me.deduction_amount.ReadOnly = True
        '
        'percentage
        '
        Me.percentage.HeaderText = "Percentage"
        Me.percentage.Name = "percentage"
        Me.percentage.ReadOnly = True
        Me.percentage.Visible = False
        '
        'completed_on
        '
        Me.completed_on.HeaderText = "Completed On"
        Me.completed_on.Name = "completed_on"
        Me.completed_on.ReadOnly = True
        Me.completed_on.Visible = False
        '
        'with_terms
        '
        Me.with_terms.HeaderText = "With Terms"
        Me.with_terms.Name = "with_terms"
        Me.with_terms.ReadOnly = True
        Me.with_terms.Visible = False
        '
        'total_term
        '
        Me.total_term.HeaderText = "Term"
        Me.total_term.Name = "total_term"
        Me.total_term.ReadOnly = True
        '
        'terms_paid
        '
        Me.terms_paid.HeaderText = "Terms Paid"
        Me.terms_paid.Name = "terms_paid"
        Me.terms_paid.ReadOnly = True
        '
        'billing_cycle
        '
        Me.billing_cycle.HeaderText = "Billing Cycle"
        Me.billing_cycle.Name = "billing_cycle"
        Me.billing_cycle.ReadOnly = True
        '
        'reason
        '
        Me.reason.HeaderText = "Remarks"
        Me.reason.Name = "reason"
        Me.reason.ReadOnly = True
        '
        'is_default
        '
        Me.is_default.HeaderText = "Is Default"
        Me.is_default.Name = "is_default"
        Me.is_default.ReadOnly = True
        Me.is_default.Visible = False
        '
        'employee_deduction_status
        '
        Me.employee_deduction_status.HeaderText = "Status"
        Me.employee_deduction_status.Name = "employee_deduction_status"
        Me.employee_deduction_status.ReadOnly = True
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
        Me.action_edit.ReadOnly = True
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
        Me.action_stop.ReadOnly = True
        '
        'FrmShowEmployeeDeduction
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.White
        Me.ClientSize = New System.Drawing.Size(998, 586)
        Me.Controls.Add(Me.panelContainer)
        Me.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        Me.FormBorderStyle = System.Windows.Forms.FormBorderStyle.None
        Me.Name = "FrmShowEmployeeDeduction"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterParent
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
    Friend WithEvents panelNav As Panel
    Friend WithEvents btnMinimize As Button
    Friend WithEvents btnClose As Button
    Friend WithEvents panelTop As Panel
    Friend WithEvents GroupBox1 As GroupBox
    Friend WithEvents lblEmployeeName As Label
    Friend WithEvents btnAdd As Button
    Friend WithEvents dgvTable As DataGridView
    Friend WithEvents number As DataGridViewTextBoxColumn
    Friend WithEvents employee_deduction_id As DataGridViewTextBoxColumn
    Friend WithEvents payroll_period_id As DataGridViewTextBoxColumn
    Friend WithEvents employee_id As DataGridViewTextBoxColumn
    Friend WithEvents deduction_id As DataGridViewTextBoxColumn
    Friend WithEvents deduction_name As DataGridViewTextBoxColumn
    Friend WithEvents deduction_code As DataGridViewTextBoxColumn
    Friend WithEvents deduction_amount As DataGridViewTextBoxColumn
    Friend WithEvents percentage As DataGridViewTextBoxColumn
    Friend WithEvents completed_on As DataGridViewTextBoxColumn
    Friend WithEvents with_terms As DataGridViewTextBoxColumn
    Friend WithEvents total_term As DataGridViewTextBoxColumn
    Friend WithEvents terms_paid As DataGridViewTextBoxColumn
    Friend WithEvents billing_cycle As DataGridViewTextBoxColumn
    Friend WithEvents reason As DataGridViewTextBoxColumn
    Friend WithEvents is_default As DataGridViewTextBoxColumn
    Friend WithEvents employee_deduction_status As DataGridViewTextBoxColumn
    Friend WithEvents action_edit As DataGridViewButtonColumn
    Friend WithEvents action_stop As DataGridViewButtonColumn
End Class
