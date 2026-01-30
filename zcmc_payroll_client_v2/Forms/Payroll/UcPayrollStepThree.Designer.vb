<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()>
Partial Class UcPayrollStepThree
    Inherits System.Windows.Forms.UserControl

    'UserControl overrides dispose to clean up the component list.
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
        Dim DataGridViewCellStyle2 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle3 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Me.panelTop = New System.Windows.Forms.Panel()
        Me.lblDescription = New System.Windows.Forms.Label()
        Me.lblTitle = New System.Windows.Forms.Label()
        Me.panelContainer = New System.Windows.Forms.Panel()
        Me.panelContent = New System.Windows.Forms.Panel()
        Me.lblMessage = New System.Windows.Forms.Label()
        Me.dgvTable = New System.Windows.Forms.DataGridView()
        Me.number_sequence = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_payroll_id = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_number = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_name = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.payroll_month = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.payroll_year = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.basic_salary = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.total_receivables = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.gross_salary = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.total_deductions = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.net_pay = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.Panel1 = New System.Windows.Forms.Panel()
        Me.lblPeriod = New System.Windows.Forms.Label()
        Me.lblPayrollType = New System.Windows.Forms.Label()
        Me.Label3 = New System.Windows.Forms.Label()
        Me.Label2 = New System.Windows.Forms.Label()
        Me.panelBottom = New System.Windows.Forms.Panel()
        Me.Panel2 = New System.Windows.Forms.Panel()
        Me.btnPrevious = New System.Windows.Forms.Button()
        Me.lblPage = New System.Windows.Forms.Label()
        Me.cmbPage = New System.Windows.Forms.ComboBox()
        Me.btnNext = New System.Windows.Forms.Button()
        Me.lblPerPage = New System.Windows.Forms.Label()
        Me.cmbPerPage = New System.Windows.Forms.ComboBox()
        Me.Label1 = New System.Windows.Forms.Label()
        Me.panelTop.SuspendLayout()
        Me.panelContainer.SuspendLayout()
        Me.panelContent.SuspendLayout()
        CType(Me.dgvTable, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.Panel1.SuspendLayout()
        Me.panelBottom.SuspendLayout()
        Me.Panel2.SuspendLayout()
        Me.SuspendLayout()
        '
        'panelTop
        '
        Me.panelTop.BackColor = System.Drawing.Color.FromArgb(CType(CType(10, Byte), Integer), CType(CType(62, Byte), Integer), CType(CType(48, Byte), Integer))
        Me.panelTop.Controls.Add(Me.lblDescription)
        Me.panelTop.Controls.Add(Me.lblTitle)
        Me.panelTop.Dock = System.Windows.Forms.DockStyle.Top
        Me.panelTop.Location = New System.Drawing.Point(0, 0)
        Me.panelTop.Name = "panelTop"
        Me.panelTop.Size = New System.Drawing.Size(1107, 66)
        Me.panelTop.TabIndex = 32
        '
        'lblDescription
        '
        Me.lblDescription.Anchor = System.Windows.Forms.AnchorStyles.Top
        Me.lblDescription.AutoSize = True
        Me.lblDescription.Font = New System.Drawing.Font("Segoe UI", 10.0!)
        Me.lblDescription.ForeColor = System.Drawing.Color.White
        Me.lblDescription.Location = New System.Drawing.Point(406, 31)
        Me.lblDescription.Name = "lblDescription"
        Me.lblDescription.Size = New System.Drawing.Size(102, 19)
        Me.lblDescription.TabIndex = 3
        Me.lblDescription.Text = "Validate Payroll"
        '
        'lblTitle
        '
        Me.lblTitle.Anchor = System.Windows.Forms.AnchorStyles.Top
        Me.lblTitle.AutoSize = True
        Me.lblTitle.Font = New System.Drawing.Font("Segoe UI", 12.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblTitle.ForeColor = System.Drawing.Color.White
        Me.lblTitle.Location = New System.Drawing.Point(407, 6)
        Me.lblTitle.Name = "lblTitle"
        Me.lblTitle.Size = New System.Drawing.Size(284, 21)
        Me.lblTitle.TabIndex = 2
        Me.lblTitle.Text = "Create Payroll - Step 3 of 4 : Preview"
        '
        'panelContainer
        '
        Me.panelContainer.Controls.Add(Me.panelContent)
        Me.panelContainer.Controls.Add(Me.panelBottom)
        Me.panelContainer.Controls.Add(Me.panelTop)
        Me.panelContainer.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContainer.Location = New System.Drawing.Point(0, 0)
        Me.panelContainer.Name = "panelContainer"
        Me.panelContainer.Size = New System.Drawing.Size(1107, 588)
        Me.panelContainer.TabIndex = 2
        '
        'panelContent
        '
        Me.panelContent.Controls.Add(Me.lblMessage)
        Me.panelContent.Controls.Add(Me.dgvTable)
        Me.panelContent.Controls.Add(Me.Panel1)
        Me.panelContent.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContent.Location = New System.Drawing.Point(0, 66)
        Me.panelContent.Name = "panelContent"
        Me.panelContent.Size = New System.Drawing.Size(1107, 455)
        Me.panelContent.TabIndex = 35
        '
        'lblMessage
        '
        Me.lblMessage.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom), System.Windows.Forms.AnchorStyles)
        Me.lblMessage.AutoSize = True
        Me.lblMessage.BackColor = System.Drawing.Color.White
        Me.lblMessage.Font = New System.Drawing.Font("Segoe UI Semibold", 15.75!, System.Drawing.FontStyle.Bold)
        Me.lblMessage.Location = New System.Drawing.Point(477, 246)
        Me.lblMessage.Name = "lblMessage"
        Me.lblMessage.Size = New System.Drawing.Size(152, 30)
        Me.lblMessage.TabIndex = 37
        Me.lblMessage.Text = "Label Message"
        Me.lblMessage.Visible = False
        '
        'dgvTable
        '
        Me.dgvTable.AllowUserToAddRows = False
        Me.dgvTable.AllowUserToDeleteRows = False
        Me.dgvTable.AllowUserToResizeColumns = False
        Me.dgvTable.AllowUserToResizeRows = False
        DataGridViewCellStyle1.Padding = New System.Windows.Forms.Padding(5)
        Me.dgvTable.AlternatingRowsDefaultCellStyle = DataGridViewCellStyle1
        Me.dgvTable.AutoSizeColumnsMode = System.Windows.Forms.DataGridViewAutoSizeColumnsMode.Fill
        Me.dgvTable.AutoSizeRowsMode = System.Windows.Forms.DataGridViewAutoSizeRowsMode.AllCellsExceptHeaders
        Me.dgvTable.BackgroundColor = System.Drawing.SystemColors.ButtonHighlight
        Me.dgvTable.BorderStyle = System.Windows.Forms.BorderStyle.None
        Me.dgvTable.CellBorderStyle = System.Windows.Forms.DataGridViewCellBorderStyle.RaisedVertical
        Me.dgvTable.ClipboardCopyMode = System.Windows.Forms.DataGridViewClipboardCopyMode.Disable
        Me.dgvTable.ColumnHeadersBorderStyle = System.Windows.Forms.DataGridViewHeaderBorderStyle.None
        DataGridViewCellStyle2.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleLeft
        DataGridViewCellStyle2.BackColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        DataGridViewCellStyle2.Font = New System.Drawing.Font("Microsoft Sans Serif", 9.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        DataGridViewCellStyle2.ForeColor = System.Drawing.Color.White
        DataGridViewCellStyle2.Padding = New System.Windows.Forms.Padding(10)
        DataGridViewCellStyle2.SelectionBackColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        DataGridViewCellStyle2.SelectionForeColor = System.Drawing.Color.White
        DataGridViewCellStyle2.WrapMode = System.Windows.Forms.DataGridViewTriState.[True]
        Me.dgvTable.ColumnHeadersDefaultCellStyle = DataGridViewCellStyle2
        Me.dgvTable.ColumnHeadersHeightSizeMode = System.Windows.Forms.DataGridViewColumnHeadersHeightSizeMode.AutoSize
        Me.dgvTable.Columns.AddRange(New System.Windows.Forms.DataGridViewColumn() {Me.number_sequence, Me.employee_payroll_id, Me.employee_number, Me.employee_name, Me.payroll_month, Me.payroll_year, Me.basic_salary, Me.total_receivables, Me.gross_salary, Me.total_deductions, Me.net_pay})
        DataGridViewCellStyle3.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleLeft
        DataGridViewCellStyle3.BackColor = System.Drawing.SystemColors.Window
        DataGridViewCellStyle3.Font = New System.Drawing.Font("Microsoft Sans Serif", 8.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        DataGridViewCellStyle3.ForeColor = System.Drawing.SystemColors.ControlText
        DataGridViewCellStyle3.Padding = New System.Windows.Forms.Padding(5)
        DataGridViewCellStyle3.SelectionBackColor = System.Drawing.SystemColors.GradientInactiveCaption
        DataGridViewCellStyle3.SelectionForeColor = System.Drawing.SystemColors.Desktop
        DataGridViewCellStyle3.WrapMode = System.Windows.Forms.DataGridViewTriState.[False]
        Me.dgvTable.DefaultCellStyle = DataGridViewCellStyle3
        Me.dgvTable.Dock = System.Windows.Forms.DockStyle.Fill
        Me.dgvTable.EditMode = System.Windows.Forms.DataGridViewEditMode.EditProgrammatically
        Me.dgvTable.EnableHeadersVisualStyles = False
        Me.dgvTable.Location = New System.Drawing.Point(0, 68)
        Me.dgvTable.Name = "dgvTable"
        Me.dgvTable.RowHeadersBorderStyle = System.Windows.Forms.DataGridViewHeaderBorderStyle.None
        Me.dgvTable.RowHeadersVisible = False
        Me.dgvTable.SelectionMode = System.Windows.Forms.DataGridViewSelectionMode.FullRowSelect
        Me.dgvTable.Size = New System.Drawing.Size(1107, 387)
        Me.dgvTable.TabIndex = 36
        '
        'number_sequence
        '
        Me.number_sequence.FillWeight = 40.0!
        Me.number_sequence.HeaderText = "No."
        Me.number_sequence.Name = "number_sequence"
        '
        'employee_payroll_id
        '
        Me.employee_payroll_id.HeaderText = "ID"
        Me.employee_payroll_id.Name = "employee_payroll_id"
        Me.employee_payroll_id.Visible = False
        '
        'employee_number
        '
        Me.employee_number.HeaderText = "Employee ID"
        Me.employee_number.Name = "employee_number"
        '
        'employee_name
        '
        Me.employee_name.HeaderText = "Name"
        Me.employee_name.Name = "employee_name"
        '
        'payroll_month
        '
        Me.payroll_month.HeaderText = "Month"
        Me.payroll_month.Name = "payroll_month"
        '
        'payroll_year
        '
        Me.payroll_year.HeaderText = "Year"
        Me.payroll_year.Name = "payroll_year"
        '
        'basic_salary
        '
        Me.basic_salary.HeaderText = "Basic"
        Me.basic_salary.Name = "basic_salary"
        '
        'total_receivables
        '
        Me.total_receivables.HeaderText = "Total Receivables"
        Me.total_receivables.Name = "total_receivables"
        '
        'gross_salary
        '
        Me.gross_salary.HeaderText = "Gross Salary"
        Me.gross_salary.Name = "gross_salary"
        '
        'total_deductions
        '
        Me.total_deductions.HeaderText = "Total Deduction"
        Me.total_deductions.Name = "total_deductions"
        '
        'net_pay
        '
        Me.net_pay.HeaderText = "Net Pay"
        Me.net_pay.Name = "net_pay"
        '
        'Panel1
        '
        Me.Panel1.BackColor = System.Drawing.Color.WhiteSmoke
        Me.Panel1.Controls.Add(Me.lblPeriod)
        Me.Panel1.Controls.Add(Me.lblPayrollType)
        Me.Panel1.Controls.Add(Me.Label3)
        Me.Panel1.Controls.Add(Me.Label2)
        Me.Panel1.Dock = System.Windows.Forms.DockStyle.Top
        Me.Panel1.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Pixel)
        Me.Panel1.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        Me.Panel1.Location = New System.Drawing.Point(0, 0)
        Me.Panel1.Name = "Panel1"
        Me.Panel1.Size = New System.Drawing.Size(1107, 68)
        Me.Panel1.TabIndex = 35
        '
        'lblPeriod
        '
        Me.lblPeriod.AutoSize = True
        Me.lblPeriod.Font = New System.Drawing.Font("Segoe UI Semibold", 14.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblPeriod.ForeColor = System.Drawing.Color.FromArgb(CType(CType(10, Byte), Integer), CType(CType(62, Byte), Integer), CType(CType(48, Byte), Integer))
        Me.lblPeriod.Location = New System.Drawing.Point(146, 28)
        Me.lblPeriod.Name = "lblPeriod"
        Me.lblPeriod.Size = New System.Drawing.Size(195, 25)
        Me.lblPeriod.TabIndex = 43
        Me.lblPeriod.Text = "January 01 - 31, 2026"
        '
        'lblPayrollType
        '
        Me.lblPayrollType.AutoSize = True
        Me.lblPayrollType.Font = New System.Drawing.Font("Segoe UI Semibold", 14.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblPayrollType.ForeColor = System.Drawing.Color.FromArgb(CType(CType(10, Byte), Integer), CType(CType(62, Byte), Integer), CType(CType(48, Byte), Integer))
        Me.lblPayrollType.Location = New System.Drawing.Point(146, 3)
        Me.lblPayrollType.Name = "lblPayrollType"
        Me.lblPayrollType.Size = New System.Drawing.Size(243, 25)
        Me.lblPayrollType.TabIndex = 42
        Me.lblPayrollType.Text = "Job Order - General Payroll"
        '
        'Label3
        '
        Me.Label3.AutoSize = True
        Me.Label3.Font = New System.Drawing.Font("Segoe UI", 14.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label3.Location = New System.Drawing.Point(3, 28)
        Me.Label3.Name = "Label3"
        Me.Label3.Size = New System.Drawing.Size(137, 25)
        Me.Label3.TabIndex = 41
        Me.Label3.Text = "Payroll Period :"
        '
        'Label2
        '
        Me.Label2.AutoSize = True
        Me.Label2.Font = New System.Drawing.Font("Segoe UI", 14.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label2.Location = New System.Drawing.Point(3, 3)
        Me.Label2.Name = "Label2"
        Me.Label2.Size = New System.Drawing.Size(122, 25)
        Me.Label2.TabIndex = 40
        Me.Label2.Text = "Payroll Type :"
        '
        'panelBottom
        '
        Me.panelBottom.BackColor = System.Drawing.Color.WhiteSmoke
        Me.panelBottom.Controls.Add(Me.Panel2)
        Me.panelBottom.Dock = System.Windows.Forms.DockStyle.Bottom
        Me.panelBottom.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Pixel)
        Me.panelBottom.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        Me.panelBottom.Location = New System.Drawing.Point(0, 521)
        Me.panelBottom.Name = "panelBottom"
        Me.panelBottom.Size = New System.Drawing.Size(1107, 67)
        Me.panelBottom.TabIndex = 34
        '
        'Panel2
        '
        Me.Panel2.Anchor = CType((((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom) _
            Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.Panel2.BackColor = System.Drawing.Color.White
        Me.Panel2.Controls.Add(Me.btnPrevious)
        Me.Panel2.Controls.Add(Me.lblPage)
        Me.Panel2.Controls.Add(Me.cmbPage)
        Me.Panel2.Controls.Add(Me.btnNext)
        Me.Panel2.Controls.Add(Me.lblPerPage)
        Me.Panel2.Controls.Add(Me.cmbPerPage)
        Me.Panel2.Controls.Add(Me.Label1)
        Me.Panel2.Location = New System.Drawing.Point(12, 10)
        Me.Panel2.Name = "Panel2"
        Me.Panel2.Size = New System.Drawing.Size(1074, 46)
        Me.Panel2.TabIndex = 2
        '
        'btnPrevious
        '
        Me.btnPrevious.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnPrevious.BackColor = System.Drawing.Color.White
        Me.btnPrevious.FlatAppearance.BorderSize = 0
        Me.btnPrevious.FlatStyle = System.Windows.Forms.FlatStyle.Popup
        Me.btnPrevious.Font = New System.Drawing.Font("Tahoma", 9.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnPrevious.ForeColor = System.Drawing.Color.Black
        Me.btnPrevious.Location = New System.Drawing.Point(831, 9)
        Me.btnPrevious.Name = "btnPrevious"
        Me.btnPrevious.Size = New System.Drawing.Size(62, 25)
        Me.btnPrevious.TabIndex = 17
        Me.btnPrevious.Text = "Prev"
        Me.btnPrevious.TextAlign = System.Drawing.ContentAlignment.MiddleRight
        Me.btnPrevious.TextImageRelation = System.Windows.Forms.TextImageRelation.ImageBeforeText
        Me.btnPrevious.UseVisualStyleBackColor = False
        '
        'lblPage
        '
        Me.lblPage.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.lblPage.AutoSize = True
        Me.lblPage.Font = New System.Drawing.Font("Tahoma", 9.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblPage.ForeColor = System.Drawing.Color.Black
        Me.lblPage.Location = New System.Drawing.Point(960, 14)
        Me.lblPage.Name = "lblPage"
        Me.lblPage.Size = New System.Drawing.Size(43, 14)
        Me.lblPage.TabIndex = 16
        Me.lblPage.Text = "of 500"
        Me.lblPage.TextAlign = System.Drawing.ContentAlignment.MiddleCenter
        '
        'cmbPage
        '
        Me.cmbPage.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.cmbPage.BackColor = System.Drawing.Color.WhiteSmoke
        Me.cmbPage.DropDownStyle = System.Windows.Forms.ComboBoxStyle.DropDownList
        Me.cmbPage.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.cmbPage.Font = New System.Drawing.Font("Tahoma", 9.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.cmbPage.FormattingEnabled = True
        Me.cmbPage.Items.AddRange(New Object() {"10", "20", "25", "50", "100"})
        Me.cmbPage.Location = New System.Drawing.Point(899, 9)
        Me.cmbPage.Name = "cmbPage"
        Me.cmbPage.Size = New System.Drawing.Size(55, 24)
        Me.cmbPage.TabIndex = 15
        '
        'btnNext
        '
        Me.btnNext.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnNext.BackColor = System.Drawing.Color.White
        Me.btnNext.FlatAppearance.BorderSize = 0
        Me.btnNext.FlatStyle = System.Windows.Forms.FlatStyle.Popup
        Me.btnNext.Font = New System.Drawing.Font("Tahoma", 9.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnNext.ForeColor = System.Drawing.Color.Black
        Me.btnNext.Location = New System.Drawing.Point(1009, 9)
        Me.btnNext.Name = "btnNext"
        Me.btnNext.Size = New System.Drawing.Size(62, 25)
        Me.btnNext.TabIndex = 14
        Me.btnNext.Text = "Next"
        Me.btnNext.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnNext.TextImageRelation = System.Windows.Forms.TextImageRelation.ImageBeforeText
        Me.btnNext.UseVisualStyleBackColor = False
        '
        'lblPerPage
        '
        Me.lblPerPage.AutoSize = True
        Me.lblPerPage.Font = New System.Drawing.Font("Tahoma", 9.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblPerPage.ForeColor = System.Drawing.Color.Black
        Me.lblPerPage.Location = New System.Drawing.Point(166, 14)
        Me.lblPerPage.Name = "lblPerPage"
        Me.lblPerPage.Size = New System.Drawing.Size(136, 14)
        Me.lblPerPage.TabIndex = 13
        Me.lblPerPage.Text = "1-25 of 500 Employeee"
        '
        'cmbPerPage
        '
        Me.cmbPerPage.BackColor = System.Drawing.Color.WhiteSmoke
        Me.cmbPerPage.DropDownStyle = System.Windows.Forms.ComboBoxStyle.DropDownList
        Me.cmbPerPage.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.cmbPerPage.Font = New System.Drawing.Font("Tahoma", 9.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.cmbPerPage.FormattingEnabled = True
        Me.cmbPerPage.Items.AddRange(New Object() {"15", "20", "25", "50", "100"})
        Me.cmbPerPage.Location = New System.Drawing.Point(105, 9)
        Me.cmbPerPage.Name = "cmbPerPage"
        Me.cmbPerPage.Size = New System.Drawing.Size(55, 24)
        Me.cmbPerPage.TabIndex = 12
        '
        'Label1
        '
        Me.Label1.AutoSize = True
        Me.Label1.Font = New System.Drawing.Font("Tahoma", 9.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label1.ForeColor = System.Drawing.Color.Black
        Me.Label1.Location = New System.Drawing.Point(8, 14)
        Me.Label1.Name = "Label1"
        Me.Label1.Size = New System.Drawing.Size(91, 14)
        Me.Label1.TabIndex = 11
        Me.Label1.Text = "Items per page"
        '
        'UcPayrollStepThree
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.White
        Me.Controls.Add(Me.panelContainer)
        Me.Name = "UcPayrollStepThree"
        Me.Size = New System.Drawing.Size(1107, 588)
        Me.panelTop.ResumeLayout(False)
        Me.panelTop.PerformLayout()
        Me.panelContainer.ResumeLayout(False)
        Me.panelContent.ResumeLayout(False)
        Me.panelContent.PerformLayout()
        CType(Me.dgvTable, System.ComponentModel.ISupportInitialize).EndInit()
        Me.Panel1.ResumeLayout(False)
        Me.Panel1.PerformLayout()
        Me.panelBottom.ResumeLayout(False)
        Me.Panel2.ResumeLayout(False)
        Me.Panel2.PerformLayout()
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents panelTop As Panel
    Friend WithEvents lblDescription As Label
    Friend WithEvents lblTitle As Label
    Friend WithEvents panelContainer As Panel
    Friend WithEvents panelContent As Panel
    Friend WithEvents panelBottom As Panel
    Friend WithEvents Panel1 As Panel
    Friend WithEvents Label2 As Label
    Friend WithEvents Label3 As Label
    Friend WithEvents lblPeriod As Label
    Friend WithEvents lblPayrollType As Label
    Friend WithEvents dgvTable As DataGridView
    Friend WithEvents number_sequence As DataGridViewTextBoxColumn
    Friend WithEvents employee_payroll_id As DataGridViewTextBoxColumn
    Friend WithEvents employee_number As DataGridViewTextBoxColumn
    Friend WithEvents employee_name As DataGridViewTextBoxColumn
    Friend WithEvents payroll_month As DataGridViewTextBoxColumn
    Friend WithEvents payroll_year As DataGridViewTextBoxColumn
    Friend WithEvents basic_salary As DataGridViewTextBoxColumn
    Friend WithEvents total_receivables As DataGridViewTextBoxColumn
    Friend WithEvents gross_salary As DataGridViewTextBoxColumn
    Friend WithEvents total_deductions As DataGridViewTextBoxColumn
    Friend WithEvents net_pay As DataGridViewTextBoxColumn
    Friend WithEvents Panel2 As Panel
    Friend WithEvents btnPrevious As Button
    Friend WithEvents lblPage As Label
    Friend WithEvents cmbPage As ComboBox
    Friend WithEvents btnNext As Button
    Friend WithEvents lblPerPage As Label
    Friend WithEvents cmbPerPage As ComboBox
    Friend WithEvents Label1 As Label
    Friend WithEvents lblMessage As Label
End Class
