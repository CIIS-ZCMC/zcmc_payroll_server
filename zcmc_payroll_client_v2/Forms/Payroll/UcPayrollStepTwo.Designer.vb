<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()>
Partial Class UcPayrollStepTwo
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
        Dim DataGridViewCellStyle4 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Me.panelContainer = New System.Windows.Forms.Panel()
        Me.panelContent = New System.Windows.Forms.Panel()
        Me.lblMessage = New System.Windows.Forms.Label()
        Me.dgvTable = New System.Windows.Forms.DataGridView()
        Me.GroupBox1 = New System.Windows.Forms.GroupBox()
        Me.btnSelectAll = New System.Windows.Forms.Button()
        Me.txtSearch = New System.Windows.Forms.TextBox()
        Me.panelPagination = New System.Windows.Forms.Panel()
        Me.Panel1 = New System.Windows.Forms.Panel()
        Me.btnPrevious = New System.Windows.Forms.Button()
        Me.lblPage = New System.Windows.Forms.Label()
        Me.cmbPage = New System.Windows.Forms.ComboBox()
        Me.btnNext = New System.Windows.Forms.Button()
        Me.lblPerPage = New System.Windows.Forms.Label()
        Me.cmbPerPage = New System.Windows.Forms.ComboBox()
        Me.Label1 = New System.Windows.Forms.Label()
        Me.panelTop = New System.Windows.Forms.Panel()
        Me.lblDescription = New System.Windows.Forms.Label()
        Me.lblTitle = New System.Windows.Forms.Label()
        Me.action_select = New System.Windows.Forms.DataGridViewCheckBoxColumn()
        Me.row_number = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_id = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_number = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_name = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_designation = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.assigned_area = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_time_record_id = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.panelContainer.SuspendLayout()
        Me.panelContent.SuspendLayout()
        CType(Me.dgvTable, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.GroupBox1.SuspendLayout()
        Me.panelPagination.SuspendLayout()
        Me.Panel1.SuspendLayout()
        Me.panelTop.SuspendLayout()
        Me.SuspendLayout()
        '
        'panelContainer
        '
        Me.panelContainer.Controls.Add(Me.panelContent)
        Me.panelContainer.Controls.Add(Me.panelPagination)
        Me.panelContainer.Controls.Add(Me.panelTop)
        Me.panelContainer.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContainer.Location = New System.Drawing.Point(0, 0)
        Me.panelContainer.Name = "panelContainer"
        Me.panelContainer.Size = New System.Drawing.Size(1107, 588)
        Me.panelContainer.TabIndex = 1
        '
        'panelContent
        '
        Me.panelContent.Controls.Add(Me.lblMessage)
        Me.panelContent.Controls.Add(Me.dgvTable)
        Me.panelContent.Controls.Add(Me.GroupBox1)
        Me.panelContent.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContent.Location = New System.Drawing.Point(0, 66)
        Me.panelContent.Name = "panelContent"
        Me.panelContent.Size = New System.Drawing.Size(1107, 455)
        Me.panelContent.TabIndex = 34
        '
        'lblMessage
        '
        Me.lblMessage.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom), System.Windows.Forms.AnchorStyles)
        Me.lblMessage.AutoSize = True
        Me.lblMessage.BackColor = System.Drawing.Color.White
        Me.lblMessage.Font = New System.Drawing.Font("Segoe UI Semibold", 15.75!, System.Drawing.FontStyle.Bold)
        Me.lblMessage.Location = New System.Drawing.Point(477, 243)
        Me.lblMessage.Name = "lblMessage"
        Me.lblMessage.Size = New System.Drawing.Size(152, 30)
        Me.lblMessage.TabIndex = 34
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
        Me.dgvTable.Columns.AddRange(New System.Windows.Forms.DataGridViewColumn() {Me.action_select, Me.row_number, Me.employee_id, Me.employee_number, Me.employee_name, Me.employee_designation, Me.assigned_area, Me.employee_time_record_id})
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
        Me.dgvTable.EnableHeadersVisualStyles = False
        Me.dgvTable.GridColor = System.Drawing.Color.WhiteSmoke
        Me.dgvTable.Location = New System.Drawing.Point(0, 61)
        Me.dgvTable.Name = "dgvTable"
        Me.dgvTable.RowHeadersBorderStyle = System.Windows.Forms.DataGridViewHeaderBorderStyle.None
        Me.dgvTable.RowHeadersVisible = False
        DataGridViewCellStyle4.Font = New System.Drawing.Font("Tahoma", 11.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.dgvTable.RowsDefaultCellStyle = DataGridViewCellStyle4
        Me.dgvTable.SelectionMode = System.Windows.Forms.DataGridViewSelectionMode.FullRowSelect
        Me.dgvTable.Size = New System.Drawing.Size(1107, 394)
        Me.dgvTable.TabIndex = 33
        '
        'GroupBox1
        '
        Me.GroupBox1.Controls.Add(Me.btnSelectAll)
        Me.GroupBox1.Controls.Add(Me.txtSearch)
        Me.GroupBox1.Dock = System.Windows.Forms.DockStyle.Top
        Me.GroupBox1.Location = New System.Drawing.Point(0, 0)
        Me.GroupBox1.Name = "GroupBox1"
        Me.GroupBox1.Size = New System.Drawing.Size(1107, 61)
        Me.GroupBox1.TabIndex = 32
        Me.GroupBox1.TabStop = False
        '
        'btnSelectAll
        '
        Me.btnSelectAll.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnSelectAll.BackColor = System.Drawing.Color.White
        Me.btnSelectAll.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnSelectAll.Font = New System.Drawing.Font("Segoe UI", 9.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnSelectAll.ForeColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.btnSelectAll.Location = New System.Drawing.Point(979, 17)
        Me.btnSelectAll.Name = "btnSelectAll"
        Me.btnSelectAll.Size = New System.Drawing.Size(122, 27)
        Me.btnSelectAll.TabIndex = 40
        Me.btnSelectAll.Text = "Select All"
        Me.btnSelectAll.UseVisualStyleBackColor = False
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
        'panelPagination
        '
        Me.panelPagination.BackColor = System.Drawing.Color.WhiteSmoke
        Me.panelPagination.Controls.Add(Me.Panel1)
        Me.panelPagination.Dock = System.Windows.Forms.DockStyle.Bottom
        Me.panelPagination.Location = New System.Drawing.Point(0, 521)
        Me.panelPagination.Name = "panelPagination"
        Me.panelPagination.Size = New System.Drawing.Size(1107, 67)
        Me.panelPagination.TabIndex = 33
        '
        'Panel1
        '
        Me.Panel1.Anchor = CType((((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom) _
            Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.Panel1.BackColor = System.Drawing.Color.White
        Me.Panel1.Controls.Add(Me.btnPrevious)
        Me.Panel1.Controls.Add(Me.lblPage)
        Me.Panel1.Controls.Add(Me.cmbPage)
        Me.Panel1.Controls.Add(Me.btnNext)
        Me.Panel1.Controls.Add(Me.lblPerPage)
        Me.Panel1.Controls.Add(Me.cmbPerPage)
        Me.Panel1.Controls.Add(Me.Label1)
        Me.Panel1.Location = New System.Drawing.Point(12, 10)
        Me.Panel1.Name = "Panel1"
        Me.Panel1.Size = New System.Drawing.Size(1082, 46)
        Me.Panel1.TabIndex = 1
        '
        'btnPrevious
        '
        Me.btnPrevious.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnPrevious.BackColor = System.Drawing.Color.White
        Me.btnPrevious.FlatAppearance.BorderSize = 0
        Me.btnPrevious.FlatStyle = System.Windows.Forms.FlatStyle.Popup
        Me.btnPrevious.Font = New System.Drawing.Font("Tahoma", 9.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnPrevious.ForeColor = System.Drawing.Color.Black
        Me.btnPrevious.Location = New System.Drawing.Point(839, 9)
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
        Me.lblPage.Location = New System.Drawing.Point(968, 14)
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
        Me.cmbPage.Location = New System.Drawing.Point(907, 9)
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
        Me.btnNext.Location = New System.Drawing.Point(1017, 9)
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
        Me.lblDescription.Location = New System.Drawing.Point(374, 31)
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
        Me.lblTitle.Location = New System.Drawing.Point(356, 6)
        Me.lblTitle.Name = "lblTitle"
        Me.lblTitle.Size = New System.Drawing.Size(394, 21)
        Me.lblTitle.TabIndex = 2
        Me.lblTitle.Text = "Create Payroll - Step 2 of 4 : Selection of Employee"
        '
        'action_select
        '
        Me.action_select.FillWeight = 20.0!
        Me.action_select.HeaderText = ""
        Me.action_select.Name = "action_select"
        '
        'row_number
        '
        Me.row_number.FillWeight = 40.0!
        Me.row_number.HeaderText = "#"
        Me.row_number.Name = "row_number"
        Me.row_number.ReadOnly = True
        '
        'employee_id
        '
        Me.employee_id.HeaderText = "ID"
        Me.employee_id.Name = "employee_id"
        Me.employee_id.ReadOnly = True
        Me.employee_id.Visible = False
        '
        'employee_number
        '
        Me.employee_number.HeaderText = "Employee ID"
        Me.employee_number.Name = "employee_number"
        Me.employee_number.ReadOnly = True
        '
        'employee_name
        '
        Me.employee_name.HeaderText = "Name"
        Me.employee_name.Name = "employee_name"
        Me.employee_name.ReadOnly = True
        '
        'employee_designation
        '
        Me.employee_designation.HeaderText = "Designation"
        Me.employee_designation.Name = "employee_designation"
        Me.employee_designation.ReadOnly = True
        '
        'assigned_area
        '
        Me.assigned_area.HeaderText = "Assign Area"
        Me.assigned_area.Name = "assigned_area"
        Me.assigned_area.ReadOnly = True
        '
        'employee_time_record_id
        '
        Me.employee_time_record_id.HeaderText = "Employee Time Record ID"
        Me.employee_time_record_id.Name = "employee_time_record_id"
        '
        'UcPayrollStepTwo
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.White
        Me.Controls.Add(Me.panelContainer)
        Me.Name = "UcPayrollStepTwo"
        Me.Size = New System.Drawing.Size(1107, 588)
        Me.panelContainer.ResumeLayout(False)
        Me.panelContent.ResumeLayout(False)
        Me.panelContent.PerformLayout()
        CType(Me.dgvTable, System.ComponentModel.ISupportInitialize).EndInit()
        Me.GroupBox1.ResumeLayout(False)
        Me.GroupBox1.PerformLayout()
        Me.panelPagination.ResumeLayout(False)
        Me.Panel1.ResumeLayout(False)
        Me.Panel1.PerformLayout()
        Me.panelTop.ResumeLayout(False)
        Me.panelTop.PerformLayout()
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents panelContainer As Panel
    Friend WithEvents panelTop As Panel
    Friend WithEvents lblDescription As Label
    Friend WithEvents lblTitle As Label
    Friend WithEvents panelContent As Panel
    Friend WithEvents lblMessage As Label
    Friend WithEvents dgvTable As DataGridView
    Friend WithEvents GroupBox1 As GroupBox
    Friend WithEvents btnSelectAll As Button
    Friend WithEvents txtSearch As TextBox
    Friend WithEvents panelPagination As Panel
    Friend WithEvents Panel1 As Panel
    Friend WithEvents btnPrevious As Button
    Friend WithEvents lblPage As Label
    Friend WithEvents cmbPage As ComboBox
    Friend WithEvents btnNext As Button
    Friend WithEvents lblPerPage As Label
    Friend WithEvents cmbPerPage As ComboBox
    Friend WithEvents Label1 As Label
    Friend WithEvents action_select As DataGridViewCheckBoxColumn
    Friend WithEvents row_number As DataGridViewTextBoxColumn
    Friend WithEvents employee_id As DataGridViewTextBoxColumn
    Friend WithEvents employee_number As DataGridViewTextBoxColumn
    Friend WithEvents employee_name As DataGridViewTextBoxColumn
    Friend WithEvents employee_designation As DataGridViewTextBoxColumn
    Friend WithEvents assigned_area As DataGridViewTextBoxColumn
    Friend WithEvents employee_time_record_id As DataGridViewTextBoxColumn
End Class
