<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class UcPayrollStepFour
    Inherits System.Windows.Forms.UserControl

    'UserControl overrides dispose to clean up the component list.
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
        Dim DataGridViewCellStyle2 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle3 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Me.panelContainer = New System.Windows.Forms.Panel()
        Me.panelContent = New System.Windows.Forms.Panel()
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
        Me.panelTop = New System.Windows.Forms.Panel()
        Me.lblDescription = New System.Windows.Forms.Label()
        Me.lblTitle = New System.Windows.Forms.Label()
        Me.txtSearch = New System.Windows.Forms.TextBox()
        Me.GroupBox1 = New System.Windows.Forms.GroupBox()
        Me.panelContainer.SuspendLayout()
        Me.panelContent.SuspendLayout()
        CType(Me.dgvTable, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.panelTop.SuspendLayout()
        Me.GroupBox1.SuspendLayout()
        Me.SuspendLayout()
        '
        'panelContainer
        '
        Me.panelContainer.Controls.Add(Me.panelContent)
        Me.panelContainer.Controls.Add(Me.panelTop)
        Me.panelContainer.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContainer.Location = New System.Drawing.Point(0, 0)
        Me.panelContainer.Name = "panelContainer"
        Me.panelContainer.Size = New System.Drawing.Size(957, 528)
        Me.panelContainer.TabIndex = 2
        '
        'panelContent
        '
        Me.panelContent.Controls.Add(Me.dgvTable)
        Me.panelContent.Controls.Add(Me.GroupBox1)
        Me.panelContent.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContent.Location = New System.Drawing.Point(0, 66)
        Me.panelContent.Name = "panelContent"
        Me.panelContent.Size = New System.Drawing.Size(957, 462)
        Me.panelContent.TabIndex = 33
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
        Me.dgvTable.BorderStyle = System.Windows.Forms.BorderStyle.Fixed3D
        Me.dgvTable.CellBorderStyle = System.Windows.Forms.DataGridViewCellBorderStyle.RaisedVertical
        Me.dgvTable.ClipboardCopyMode = System.Windows.Forms.DataGridViewClipboardCopyMode.Disable
        Me.dgvTable.ColumnHeadersBorderStyle = System.Windows.Forms.DataGridViewHeaderBorderStyle.[Single]
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
        Me.dgvTable.Location = New System.Drawing.Point(0, 61)
        Me.dgvTable.Name = "dgvTable"
        Me.dgvTable.RowHeadersBorderStyle = System.Windows.Forms.DataGridViewHeaderBorderStyle.None
        Me.dgvTable.RowHeadersVisible = False
        Me.dgvTable.SelectionMode = System.Windows.Forms.DataGridViewSelectionMode.FullRowSelect
        Me.dgvTable.Size = New System.Drawing.Size(957, 401)
        Me.dgvTable.TabIndex = 33
        '
        'number_sequence
        '
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
        'panelTop
        '
        Me.panelTop.BackColor = System.Drawing.Color.FromArgb(CType(CType(10, Byte), Integer), CType(CType(62, Byte), Integer), CType(CType(48, Byte), Integer))
        Me.panelTop.Controls.Add(Me.lblDescription)
        Me.panelTop.Controls.Add(Me.lblTitle)
        Me.panelTop.Dock = System.Windows.Forms.DockStyle.Top
        Me.panelTop.Location = New System.Drawing.Point(0, 0)
        Me.panelTop.Name = "panelTop"
        Me.panelTop.Size = New System.Drawing.Size(957, 66)
        Me.panelTop.TabIndex = 32
        '
        'lblDescription
        '
        Me.lblDescription.Anchor = System.Windows.Forms.AnchorStyles.Top
        Me.lblDescription.AutoSize = True
        Me.lblDescription.Font = New System.Drawing.Font("Segoe UI", 10.0!)
        Me.lblDescription.ForeColor = System.Drawing.Color.White
        Me.lblDescription.Location = New System.Drawing.Point(299, 31)
        Me.lblDescription.Name = "lblDescription"
        Me.lblDescription.Size = New System.Drawing.Size(358, 19)
        Me.lblDescription.TabIndex = 3
        Me.lblDescription.Text = "This change will be only applied to the selected employee"
        '
        'lblTitle
        '
        Me.lblTitle.Anchor = System.Windows.Forms.AnchorStyles.Top
        Me.lblTitle.AutoSize = True
        Me.lblTitle.Font = New System.Drawing.Font("Segoe UI", 12.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblTitle.ForeColor = System.Drawing.Color.White
        Me.lblTitle.Location = New System.Drawing.Point(281, 6)
        Me.lblTitle.Name = "lblTitle"
        Me.lblTitle.Size = New System.Drawing.Size(394, 21)
        Me.lblTitle.TabIndex = 2
        Me.lblTitle.Text = "Create Payroll - Step 2 of 4 : Selection of Employee"
        '
        'txtSearch
        '
        Me.txtSearch.BackColor = System.Drawing.Color.White
        Me.txtSearch.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle
        Me.txtSearch.Font = New System.Drawing.Font("Segoe UI Semibold", 9.75!, CType((System.Drawing.FontStyle.Bold Or System.Drawing.FontStyle.Italic), System.Drawing.FontStyle), System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.txtSearch.ForeColor = System.Drawing.Color.Gray
        Me.txtSearch.Location = New System.Drawing.Point(6, 18)
        Me.txtSearch.Name = "txtSearch"
        Me.txtSearch.Size = New System.Drawing.Size(186, 25)
        Me.txtSearch.TabIndex = 35
        Me.txtSearch.Text = "Type Employee Name"
        '
        'GroupBox1
        '
        Me.GroupBox1.Controls.Add(Me.txtSearch)
        Me.GroupBox1.Dock = System.Windows.Forms.DockStyle.Top
        Me.GroupBox1.Location = New System.Drawing.Point(0, 0)
        Me.GroupBox1.Name = "GroupBox1"
        Me.GroupBox1.Size = New System.Drawing.Size(957, 61)
        Me.GroupBox1.TabIndex = 32
        Me.GroupBox1.TabStop = False
        '
        'UcPayrollStepFour
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.White
        Me.Controls.Add(Me.panelContainer)
        Me.Name = "UcPayrollStepFour"
        Me.Size = New System.Drawing.Size(957, 528)
        Me.panelContainer.ResumeLayout(False)
        Me.panelContent.ResumeLayout(False)
        CType(Me.dgvTable, System.ComponentModel.ISupportInitialize).EndInit()
        Me.panelTop.ResumeLayout(False)
        Me.panelTop.PerformLayout()
        Me.GroupBox1.ResumeLayout(False)
        Me.GroupBox1.PerformLayout()
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents panelContainer As Panel
    Friend WithEvents panelContent As Panel
    Friend WithEvents panelTop As Panel
    Friend WithEvents lblDescription As Label
    Friend WithEvents lblTitle As Label
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
    Friend WithEvents GroupBox1 As GroupBox
    Friend WithEvents txtSearch As TextBox
End Class
