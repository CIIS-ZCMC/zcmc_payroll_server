<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()>
Partial Class UcManageEmployee
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
        Me.components = New System.ComponentModel.Container()
        Dim DataGridViewCellStyle1 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle2 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle3 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle4 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle5 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle6 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle7 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle8 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim resources As System.ComponentModel.ComponentResourceManager = New System.ComponentModel.ComponentResourceManager(GetType(UcManageEmployee))
        Dim DataGridViewCellStyle9 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Me.panelContainer = New System.Windows.Forms.Panel()
        Me.SplitContainer = New System.Windows.Forms.SplitContainer()
        Me.lblMessage = New System.Windows.Forms.Label()
        Me.dgvTable = New System.Windows.Forms.DataGridView()
        Me.row_number = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employeeID = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_number = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_name = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_designation = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_assigned_area = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_remarks = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_status = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.action_view = New System.Windows.Forms.DataGridViewButtonColumn()
        Me.action_exclude_or_inlucde = New System.Windows.Forms.DataGridViewButtonColumn()
        Me.action_manage_deduction = New System.Windows.Forms.DataGridViewButtonColumn()
        Me.action_manage_receivables = New System.Windows.Forms.DataGridViewButtonColumn()
        Me.Panel2 = New System.Windows.Forms.Panel()
        Me.dgvList = New System.Windows.Forms.DataGridView()
        Me.record_row_number = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.record_id = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.record_type = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.record_value = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.GroupBox1 = New System.Windows.Forms.GroupBox()
        Me.lblEmployeeID = New System.Windows.Forms.Label()
        Me.lblAssignedArea = New System.Windows.Forms.Label()
        Me.lblDesignation = New System.Windows.Forms.Label()
        Me.lblName = New System.Windows.Forms.Label()
        Me.Panel3 = New System.Windows.Forms.Panel()
        Me.btnClose = New System.Windows.Forms.Button()
        Me.panelButton = New System.Windows.Forms.Panel()
        Me.panelPagination = New System.Windows.Forms.Panel()
        Me.btnPrevious = New System.Windows.Forms.Button()
        Me.lblPage = New System.Windows.Forms.Label()
        Me.cmbPage = New System.Windows.Forms.ComboBox()
        Me.btnNext = New System.Windows.Forms.Button()
        Me.lblPerPage = New System.Windows.Forms.Label()
        Me.cmbPerPage = New System.Windows.Forms.ComboBox()
        Me.Label1 = New System.Windows.Forms.Label()
        Me.panelTop = New System.Windows.Forms.Panel()
        Me.Panel4 = New System.Windows.Forms.Panel()
        Me.btnExcludedEmployee = New System.Windows.Forms.Button()
        Me.lblTitle = New System.Windows.Forms.Label()
        Me.btnIncludedEmployee = New System.Windows.Forms.Button()
        Me.DataGridViewImageColumn1 = New System.Windows.Forms.DataGridViewImageColumn()
        Me.DataGridViewImageColumn2 = New System.Windows.Forms.DataGridViewImageColumn()
        Me.tmrSlide = New System.Windows.Forms.Timer(Me.components)
        Me.panelContainer.SuspendLayout()
        CType(Me.SplitContainer, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.SplitContainer.Panel1.SuspendLayout()
        Me.SplitContainer.Panel2.SuspendLayout()
        Me.SplitContainer.SuspendLayout()
        CType(Me.dgvTable, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.Panel2.SuspendLayout()
        CType(Me.dgvList, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.GroupBox1.SuspendLayout()
        Me.Panel3.SuspendLayout()
        Me.panelButton.SuspendLayout()
        Me.panelPagination.SuspendLayout()
        Me.panelTop.SuspendLayout()
        Me.Panel4.SuspendLayout()
        Me.SuspendLayout()
        '
        'panelContainer
        '
        Me.panelContainer.Controls.Add(Me.SplitContainer)
        Me.panelContainer.Controls.Add(Me.panelButton)
        Me.panelContainer.Controls.Add(Me.panelTop)
        Me.panelContainer.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContainer.Location = New System.Drawing.Point(0, 0)
        Me.panelContainer.Name = "panelContainer"
        Me.panelContainer.Size = New System.Drawing.Size(1107, 588)
        Me.panelContainer.TabIndex = 0
        '
        'SplitContainer
        '
        Me.SplitContainer.Anchor = CType((((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom) _
            Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.SplitContainer.IsSplitterFixed = True
        Me.SplitContainer.Location = New System.Drawing.Point(13, 59)
        Me.SplitContainer.Name = "SplitContainer"
        '
        'SplitContainer.Panel1
        '
        Me.SplitContainer.Panel1.Controls.Add(Me.lblMessage)
        Me.SplitContainer.Panel1.Controls.Add(Me.dgvTable)
        '
        'SplitContainer.Panel2
        '
        Me.SplitContainer.Panel2.Controls.Add(Me.Panel2)
        Me.SplitContainer.Panel2MinSize = 0
        Me.SplitContainer.Size = New System.Drawing.Size(1082, 449)
        Me.SplitContainer.SplitterDistance = 722
        Me.SplitContainer.TabIndex = 3
        '
        'lblMessage
        '
        Me.lblMessage.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom), System.Windows.Forms.AnchorStyles)
        Me.lblMessage.AutoSize = True
        Me.lblMessage.BackColor = System.Drawing.Color.White
        Me.lblMessage.Font = New System.Drawing.Font("Segoe UI Semibold", 15.75!, System.Drawing.FontStyle.Bold)
        Me.lblMessage.Location = New System.Drawing.Point(285, 209)
        Me.lblMessage.Name = "lblMessage"
        Me.lblMessage.Size = New System.Drawing.Size(152, 30)
        Me.lblMessage.TabIndex = 11
        Me.lblMessage.Text = "Label Message"
        Me.lblMessage.Visible = False
        '
        'dgvTable
        '
        Me.dgvTable.AllowUserToAddRows = False
        Me.dgvTable.AllowUserToDeleteRows = False
        Me.dgvTable.AutoSizeColumnsMode = System.Windows.Forms.DataGridViewAutoSizeColumnsMode.Fill
        Me.dgvTable.BackgroundColor = System.Drawing.Color.White
        Me.dgvTable.BorderStyle = System.Windows.Forms.BorderStyle.None
        Me.dgvTable.ColumnHeadersBorderStyle = System.Windows.Forms.DataGridViewHeaderBorderStyle.None
        DataGridViewCellStyle1.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleLeft
        DataGridViewCellStyle1.BackColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        DataGridViewCellStyle1.Font = New System.Drawing.Font("Tahoma", 14.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        DataGridViewCellStyle1.ForeColor = System.Drawing.Color.White
        DataGridViewCellStyle1.SelectionBackColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        DataGridViewCellStyle1.SelectionForeColor = System.Drawing.Color.White
        DataGridViewCellStyle1.WrapMode = System.Windows.Forms.DataGridViewTriState.[True]
        Me.dgvTable.ColumnHeadersDefaultCellStyle = DataGridViewCellStyle1
        Me.dgvTable.ColumnHeadersHeightSizeMode = System.Windows.Forms.DataGridViewColumnHeadersHeightSizeMode.AutoSize
        Me.dgvTable.Columns.AddRange(New System.Windows.Forms.DataGridViewColumn() {Me.row_number, Me.employeeID, Me.employee_number, Me.employee_name, Me.employee_designation, Me.employee_assigned_area, Me.employee_remarks, Me.employee_status, Me.action_view, Me.action_exclude_or_inlucde, Me.action_manage_deduction, Me.action_manage_receivables})
        DataGridViewCellStyle2.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleLeft
        DataGridViewCellStyle2.BackColor = System.Drawing.Color.White
        DataGridViewCellStyle2.Font = New System.Drawing.Font("Tahoma", 9.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        DataGridViewCellStyle2.ForeColor = System.Drawing.SystemColors.ControlText
        DataGridViewCellStyle2.SelectionBackColor = System.Drawing.Color.Gainsboro
        DataGridViewCellStyle2.SelectionForeColor = System.Drawing.Color.Black
        DataGridViewCellStyle2.WrapMode = System.Windows.Forms.DataGridViewTriState.[False]
        Me.dgvTable.DefaultCellStyle = DataGridViewCellStyle2
        Me.dgvTable.Dock = System.Windows.Forms.DockStyle.Fill
        Me.dgvTable.EnableHeadersVisualStyles = False
        Me.dgvTable.GridColor = System.Drawing.Color.WhiteSmoke
        Me.dgvTable.Location = New System.Drawing.Point(0, 0)
        Me.dgvTable.MultiSelect = False
        Me.dgvTable.Name = "dgvTable"
        Me.dgvTable.ReadOnly = True
        Me.dgvTable.RowHeadersBorderStyle = System.Windows.Forms.DataGridViewHeaderBorderStyle.None
        DataGridViewCellStyle3.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleLeft
        DataGridViewCellStyle3.BackColor = System.Drawing.Color.White
        DataGridViewCellStyle3.Font = New System.Drawing.Font("Microsoft Sans Serif", 8.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        DataGridViewCellStyle3.ForeColor = System.Drawing.Color.Black
        DataGridViewCellStyle3.SelectionBackColor = System.Drawing.Color.SteelBlue
        DataGridViewCellStyle3.SelectionForeColor = System.Drawing.SystemColors.HighlightText
        DataGridViewCellStyle3.WrapMode = System.Windows.Forms.DataGridViewTriState.[True]
        Me.dgvTable.RowHeadersDefaultCellStyle = DataGridViewCellStyle3
        Me.dgvTable.RowHeadersVisible = False
        DataGridViewCellStyle4.Font = New System.Drawing.Font("Tahoma", 11.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.dgvTable.RowsDefaultCellStyle = DataGridViewCellStyle4
        Me.dgvTable.RowTemplate.Height = 26
        Me.dgvTable.SelectionMode = System.Windows.Forms.DataGridViewSelectionMode.FullRowSelect
        Me.dgvTable.Size = New System.Drawing.Size(722, 449)
        Me.dgvTable.TabIndex = 1
        '
        'row_number
        '
        Me.row_number.FillWeight = 10.0!
        Me.row_number.HeaderText = "#"
        Me.row_number.Name = "row_number"
        Me.row_number.ReadOnly = True
        '
        'employeeID
        '
        Me.employeeID.HeaderText = "ID"
        Me.employeeID.Name = "employeeID"
        Me.employeeID.ReadOnly = True
        Me.employeeID.Visible = False
        '
        'employee_number
        '
        Me.employee_number.FillWeight = 50.0!
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
        Me.employee_designation.FillWeight = 50.0!
        Me.employee_designation.HeaderText = "Designation"
        Me.employee_designation.Name = "employee_designation"
        Me.employee_designation.ReadOnly = True
        '
        'employee_assigned_area
        '
        Me.employee_assigned_area.FillWeight = 50.0!
        Me.employee_assigned_area.HeaderText = "Assigned Area"
        Me.employee_assigned_area.Name = "employee_assigned_area"
        Me.employee_assigned_area.ReadOnly = True
        '
        'employee_remarks
        '
        Me.employee_remarks.FillWeight = 50.0!
        Me.employee_remarks.HeaderText = "Remarks"
        Me.employee_remarks.Name = "employee_remarks"
        Me.employee_remarks.ReadOnly = True
        '
        'employee_status
        '
        Me.employee_status.FillWeight = 40.0!
        Me.employee_status.HeaderText = "Status"
        Me.employee_status.Name = "employee_status"
        Me.employee_status.ReadOnly = True
        '
        'action_view
        '
        Me.action_view.FillWeight = 30.0!
        Me.action_view.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.action_view.HeaderText = ""
        Me.action_view.Name = "action_view"
        Me.action_view.ReadOnly = True
        '
        'action_exclude_or_inlucde
        '
        Me.action_exclude_or_inlucde.FillWeight = 30.0!
        Me.action_exclude_or_inlucde.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.action_exclude_or_inlucde.HeaderText = ""
        Me.action_exclude_or_inlucde.Name = "action_exclude_or_inlucde"
        Me.action_exclude_or_inlucde.ReadOnly = True
        '
        'action_manage_deduction
        '
        Me.action_manage_deduction.FillWeight = 40.0!
        Me.action_manage_deduction.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.action_manage_deduction.HeaderText = ""
        Me.action_manage_deduction.Name = "action_manage_deduction"
        Me.action_manage_deduction.ReadOnly = True
        '
        'action_manage_receivables
        '
        Me.action_manage_receivables.FillWeight = 40.0!
        Me.action_manage_receivables.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.action_manage_receivables.HeaderText = ""
        Me.action_manage_receivables.Name = "action_manage_receivables"
        Me.action_manage_receivables.ReadOnly = True
        '
        'Panel2
        '
        Me.Panel2.BackColor = System.Drawing.Color.White
        Me.Panel2.Controls.Add(Me.dgvList)
        Me.Panel2.Controls.Add(Me.GroupBox1)
        Me.Panel2.Controls.Add(Me.Panel3)
        Me.Panel2.Dock = System.Windows.Forms.DockStyle.Fill
        Me.Panel2.Location = New System.Drawing.Point(0, 0)
        Me.Panel2.Name = "Panel2"
        Me.Panel2.Size = New System.Drawing.Size(356, 449)
        Me.Panel2.TabIndex = 0
        '
        'dgvList
        '
        Me.dgvList.AllowUserToAddRows = False
        Me.dgvList.AllowUserToDeleteRows = False
        Me.dgvList.AutoSizeColumnsMode = System.Windows.Forms.DataGridViewAutoSizeColumnsMode.Fill
        Me.dgvList.BackgroundColor = System.Drawing.Color.White
        Me.dgvList.BorderStyle = System.Windows.Forms.BorderStyle.None
        Me.dgvList.ColumnHeadersBorderStyle = System.Windows.Forms.DataGridViewHeaderBorderStyle.None
        DataGridViewCellStyle5.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleLeft
        DataGridViewCellStyle5.BackColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        DataGridViewCellStyle5.Font = New System.Drawing.Font("Tahoma", 14.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        DataGridViewCellStyle5.ForeColor = System.Drawing.Color.White
        DataGridViewCellStyle5.SelectionBackColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        DataGridViewCellStyle5.SelectionForeColor = System.Drawing.Color.White
        DataGridViewCellStyle5.WrapMode = System.Windows.Forms.DataGridViewTriState.[True]
        Me.dgvList.ColumnHeadersDefaultCellStyle = DataGridViewCellStyle5
        Me.dgvList.ColumnHeadersHeightSizeMode = System.Windows.Forms.DataGridViewColumnHeadersHeightSizeMode.AutoSize
        Me.dgvList.Columns.AddRange(New System.Windows.Forms.DataGridViewColumn() {Me.record_row_number, Me.record_id, Me.record_type, Me.record_value})
        DataGridViewCellStyle6.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleLeft
        DataGridViewCellStyle6.BackColor = System.Drawing.SystemColors.Window
        DataGridViewCellStyle6.Font = New System.Drawing.Font("Microsoft Sans Serif", 8.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        DataGridViewCellStyle6.ForeColor = System.Drawing.SystemColors.ControlText
        DataGridViewCellStyle6.SelectionBackColor = System.Drawing.SystemColors.Window
        DataGridViewCellStyle6.SelectionForeColor = System.Drawing.SystemColors.ControlText
        DataGridViewCellStyle6.WrapMode = System.Windows.Forms.DataGridViewTriState.[False]
        Me.dgvList.DefaultCellStyle = DataGridViewCellStyle6
        Me.dgvList.Dock = System.Windows.Forms.DockStyle.Fill
        Me.dgvList.EnableHeadersVisualStyles = False
        Me.dgvList.GridColor = System.Drawing.Color.WhiteSmoke
        Me.dgvList.Location = New System.Drawing.Point(0, 206)
        Me.dgvList.Name = "dgvList"
        DataGridViewCellStyle7.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleLeft
        DataGridViewCellStyle7.BackColor = System.Drawing.Color.White
        DataGridViewCellStyle7.Font = New System.Drawing.Font("Microsoft Sans Serif", 8.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        DataGridViewCellStyle7.ForeColor = System.Drawing.Color.Black
        DataGridViewCellStyle7.SelectionBackColor = System.Drawing.Color.White
        DataGridViewCellStyle7.SelectionForeColor = System.Drawing.Color.Black
        DataGridViewCellStyle7.WrapMode = System.Windows.Forms.DataGridViewTriState.[True]
        Me.dgvList.RowHeadersDefaultCellStyle = DataGridViewCellStyle7
        Me.dgvList.RowHeadersVisible = False
        Me.dgvList.SelectionMode = System.Windows.Forms.DataGridViewSelectionMode.FullRowSelect
        Me.dgvList.Size = New System.Drawing.Size(356, 243)
        Me.dgvList.TabIndex = 7
        '
        'record_row_number
        '
        Me.record_row_number.FillWeight = 15.0!
        Me.record_row_number.HeaderText = "#"
        Me.record_row_number.Name = "record_row_number"
        '
        'record_id
        '
        Me.record_id.HeaderText = "ID"
        Me.record_id.Name = "record_id"
        Me.record_id.Visible = False
        '
        'record_type
        '
        Me.record_type.HeaderText = "Type"
        Me.record_type.Name = "record_type"
        '
        'record_value
        '
        Me.record_value.HeaderText = "Value"
        Me.record_value.Name = "record_value"
        '
        'GroupBox1
        '
        Me.GroupBox1.Controls.Add(Me.lblEmployeeID)
        Me.GroupBox1.Controls.Add(Me.lblAssignedArea)
        Me.GroupBox1.Controls.Add(Me.lblDesignation)
        Me.GroupBox1.Controls.Add(Me.lblName)
        Me.GroupBox1.Dock = System.Windows.Forms.DockStyle.Top
        Me.GroupBox1.Font = New System.Drawing.Font("Tahoma", 14.25!)
        Me.GroupBox1.ForeColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.GroupBox1.Location = New System.Drawing.Point(0, 42)
        Me.GroupBox1.Name = "GroupBox1"
        Me.GroupBox1.Size = New System.Drawing.Size(356, 164)
        Me.GroupBox1.TabIndex = 6
        Me.GroupBox1.TabStop = False
        Me.GroupBox1.Text = "Employee Information"
        '
        'lblEmployeeID
        '
        Me.lblEmployeeID.AutoSize = True
        Me.lblEmployeeID.Font = New System.Drawing.Font("Tahoma", 9.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblEmployeeID.ForeColor = System.Drawing.Color.Black
        Me.lblEmployeeID.Location = New System.Drawing.Point(18, 26)
        Me.lblEmployeeID.Name = "lblEmployeeID"
        Me.lblEmployeeID.Size = New System.Drawing.Size(77, 14)
        Me.lblEmployeeID.TabIndex = 12
        Me.lblEmployeeID.Text = "2026011551"
        '
        'lblAssignedArea
        '
        Me.lblAssignedArea.Font = New System.Drawing.Font("Tahoma", 9.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblAssignedArea.ForeColor = System.Drawing.Color.Black
        Me.lblAssignedArea.Location = New System.Drawing.Point(18, 80)
        Me.lblAssignedArea.Name = "lblAssignedArea"
        Me.lblAssignedArea.Size = New System.Drawing.Size(312, 74)
        Me.lblAssignedArea.TabIndex = 11
        Me.lblAssignedArea.Text = "Innovation and Information System Unit"
        '
        'lblDesignation
        '
        Me.lblDesignation.AutoSize = True
        Me.lblDesignation.Font = New System.Drawing.Font("Tahoma", 9.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblDesignation.ForeColor = System.Drawing.Color.Black
        Me.lblDesignation.Location = New System.Drawing.Point(18, 66)
        Me.lblDesignation.Name = "lblDesignation"
        Me.lblDesignation.Size = New System.Drawing.Size(143, 14)
        Me.lblDesignation.TabIndex = 10
        Me.lblDesignation.Text = "Computer Programmer II"
        '
        'lblName
        '
        Me.lblName.AutoSize = True
        Me.lblName.Font = New System.Drawing.Font("Tahoma", 14.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblName.ForeColor = System.Drawing.Color.Black
        Me.lblName.Location = New System.Drawing.Point(17, 41)
        Me.lblName.Name = "lblName"
        Me.lblName.Size = New System.Drawing.Size(185, 23)
        Me.lblName.TabIndex = 9
        Me.lblName.Text = "Dolar, Kim Horace A."
        '
        'Panel3
        '
        Me.Panel3.Controls.Add(Me.btnClose)
        Me.Panel3.Dock = System.Windows.Forms.DockStyle.Top
        Me.Panel3.Location = New System.Drawing.Point(0, 0)
        Me.Panel3.Name = "Panel3"
        Me.Panel3.Size = New System.Drawing.Size(356, 42)
        Me.Panel3.TabIndex = 5
        '
        'btnClose
        '
        Me.btnClose.BackColor = System.Drawing.Color.White
        Me.btnClose.FlatAppearance.BorderSize = 0
        Me.btnClose.FlatStyle = System.Windows.Forms.FlatStyle.Popup
        Me.btnClose.Font = New System.Drawing.Font("Tahoma", 12.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnClose.ForeColor = System.Drawing.Color.Black
        Me.btnClose.Image = Global.zcmc_payroll_client_v2.My.Resources.Resources.back_button_24
        Me.btnClose.Location = New System.Drawing.Point(3, 3)
        Me.btnClose.Name = "btnClose"
        Me.btnClose.Size = New System.Drawing.Size(81, 33)
        Me.btnClose.TabIndex = 2
        Me.btnClose.Text = "Close"
        Me.btnClose.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnClose.TextImageRelation = System.Windows.Forms.TextImageRelation.ImageBeforeText
        Me.btnClose.UseVisualStyleBackColor = False
        '
        'panelButton
        '
        Me.panelButton.Controls.Add(Me.panelPagination)
        Me.panelButton.Dock = System.Windows.Forms.DockStyle.Bottom
        Me.panelButton.Location = New System.Drawing.Point(0, 521)
        Me.panelButton.Name = "panelButton"
        Me.panelButton.Size = New System.Drawing.Size(1107, 67)
        Me.panelButton.TabIndex = 2
        '
        'panelPagination
        '
        Me.panelPagination.Anchor = CType((((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom) _
            Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.panelPagination.BackColor = System.Drawing.Color.White
        Me.panelPagination.Controls.Add(Me.btnPrevious)
        Me.panelPagination.Controls.Add(Me.lblPage)
        Me.panelPagination.Controls.Add(Me.cmbPage)
        Me.panelPagination.Controls.Add(Me.btnNext)
        Me.panelPagination.Controls.Add(Me.lblPerPage)
        Me.panelPagination.Controls.Add(Me.cmbPerPage)
        Me.panelPagination.Controls.Add(Me.Label1)
        Me.panelPagination.Location = New System.Drawing.Point(14, 11)
        Me.panelPagination.Name = "panelPagination"
        Me.panelPagination.Size = New System.Drawing.Size(1082, 46)
        Me.panelPagination.TabIndex = 0
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
        Me.panelTop.Controls.Add(Me.Panel4)
        Me.panelTop.Dock = System.Windows.Forms.DockStyle.Top
        Me.panelTop.Location = New System.Drawing.Point(0, 0)
        Me.panelTop.Name = "panelTop"
        Me.panelTop.Size = New System.Drawing.Size(1107, 56)
        Me.panelTop.TabIndex = 0
        '
        'Panel4
        '
        Me.Panel4.Anchor = CType((((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom) _
            Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.Panel4.BackColor = System.Drawing.Color.White
        Me.Panel4.Controls.Add(Me.btnExcludedEmployee)
        Me.Panel4.Controls.Add(Me.lblTitle)
        Me.Panel4.Controls.Add(Me.btnIncludedEmployee)
        Me.Panel4.Location = New System.Drawing.Point(14, 8)
        Me.Panel4.Name = "Panel4"
        Me.Panel4.Size = New System.Drawing.Size(1082, 43)
        Me.Panel4.TabIndex = 3
        '
        'btnExcludedEmployee
        '
        Me.btnExcludedEmployee.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnExcludedEmployee.BackColor = System.Drawing.Color.Gainsboro
        Me.btnExcludedEmployee.FlatAppearance.BorderColor = System.Drawing.Color.White
        Me.btnExcludedEmployee.FlatAppearance.BorderSize = 0
        Me.btnExcludedEmployee.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnExcludedEmployee.Font = New System.Drawing.Font("Tahoma", 12.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnExcludedEmployee.ForeColor = System.Drawing.Color.Black
        Me.btnExcludedEmployee.Location = New System.Drawing.Point(924, 7)
        Me.btnExcludedEmployee.Name = "btnExcludedEmployee"
        Me.btnExcludedEmployee.Size = New System.Drawing.Size(155, 29)
        Me.btnExcludedEmployee.TabIndex = 1
        Me.btnExcludedEmployee.Text = "Excluded Employee"
        Me.btnExcludedEmployee.UseVisualStyleBackColor = False
        '
        'lblTitle
        '
        Me.lblTitle.AutoSize = True
        Me.lblTitle.Font = New System.Drawing.Font("Tahoma", 15.75!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblTitle.ForeColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.lblTitle.Location = New System.Drawing.Point(6, 9)
        Me.lblTitle.Name = "lblTitle"
        Me.lblTitle.Size = New System.Drawing.Size(344, 25)
        Me.lblTitle.TabIndex = 2
        Me.lblTitle.Text = "Zamboanga City Medical Center"
        Me.lblTitle.TextAlign = System.Drawing.ContentAlignment.MiddleCenter
        '
        'btnIncludedEmployee
        '
        Me.btnIncludedEmployee.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnIncludedEmployee.BackColor = System.Drawing.Color.Gainsboro
        Me.btnIncludedEmployee.FlatAppearance.BorderColor = System.Drawing.Color.White
        Me.btnIncludedEmployee.FlatAppearance.BorderSize = 0
        Me.btnIncludedEmployee.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnIncludedEmployee.Font = New System.Drawing.Font("Tahoma", 12.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnIncludedEmployee.ForeColor = System.Drawing.Color.Black
        Me.btnIncludedEmployee.Location = New System.Drawing.Point(763, 7)
        Me.btnIncludedEmployee.Name = "btnIncludedEmployee"
        Me.btnIncludedEmployee.Size = New System.Drawing.Size(155, 29)
        Me.btnIncludedEmployee.TabIndex = 0
        Me.btnIncludedEmployee.Text = "Included Employee"
        Me.btnIncludedEmployee.UseVisualStyleBackColor = False
        '
        'DataGridViewImageColumn1
        '
        DataGridViewCellStyle8.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleCenter
        DataGridViewCellStyle8.BackColor = System.Drawing.Color.White
        DataGridViewCellStyle8.ForeColor = System.Drawing.Color.White
        DataGridViewCellStyle8.NullValue = CType(resources.GetObject("DataGridViewCellStyle8.NullValue"), Object)
        DataGridViewCellStyle8.SelectionBackColor = System.Drawing.Color.WhiteSmoke
        DataGridViewCellStyle8.SelectionForeColor = System.Drawing.Color.Black
        Me.DataGridViewImageColumn1.DefaultCellStyle = DataGridViewCellStyle8
        Me.DataGridViewImageColumn1.FillWeight = 40.0!
        Me.DataGridViewImageColumn1.HeaderText = ""
        Me.DataGridViewImageColumn1.Image = Global.zcmc_payroll_client_v2.My.Resources.Resources.view_employee_24
        Me.DataGridViewImageColumn1.ImageLayout = System.Windows.Forms.DataGridViewImageCellLayout.Stretch
        Me.DataGridViewImageColumn1.Name = "DataGridViewImageColumn1"
        Me.DataGridViewImageColumn1.Resizable = System.Windows.Forms.DataGridViewTriState.[True]
        Me.DataGridViewImageColumn1.ToolTipText = "View"
        Me.DataGridViewImageColumn1.Width = 62
        '
        'DataGridViewImageColumn2
        '
        DataGridViewCellStyle9.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleCenter
        DataGridViewCellStyle9.BackColor = System.Drawing.Color.White
        DataGridViewCellStyle9.ForeColor = System.Drawing.Color.White
        DataGridViewCellStyle9.NullValue = CType(resources.GetObject("DataGridViewCellStyle9.NullValue"), Object)
        DataGridViewCellStyle9.SelectionBackColor = System.Drawing.Color.Gainsboro
        DataGridViewCellStyle9.SelectionForeColor = System.Drawing.Color.Black
        Me.DataGridViewImageColumn2.DefaultCellStyle = DataGridViewCellStyle9
        Me.DataGridViewImageColumn2.FillWeight = 40.0!
        Me.DataGridViewImageColumn2.HeaderText = ""
        Me.DataGridViewImageColumn2.Image = Global.zcmc_payroll_client_v2.My.Resources.Resources.remove_employee_24
        Me.DataGridViewImageColumn2.ImageLayout = System.Windows.Forms.DataGridViewImageCellLayout.Stretch
        Me.DataGridViewImageColumn2.Name = "DataGridViewImageColumn2"
        Me.DataGridViewImageColumn2.Resizable = System.Windows.Forms.DataGridViewTriState.[True]
        Me.DataGridViewImageColumn2.ToolTipText = "Exclude"
        Me.DataGridViewImageColumn2.Width = 62
        '
        'tmrSlide
        '
        Me.tmrSlide.Interval = 10
        '
        'UcManageEmployee
        '
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.None
        Me.AutoSize = True
        Me.BackColor = System.Drawing.Color.WhiteSmoke
        Me.Controls.Add(Me.panelContainer)
        Me.Name = "UcManageEmployee"
        Me.Size = New System.Drawing.Size(1107, 588)
        Me.panelContainer.ResumeLayout(False)
        Me.SplitContainer.Panel1.ResumeLayout(False)
        Me.SplitContainer.Panel1.PerformLayout()
        Me.SplitContainer.Panel2.ResumeLayout(False)
        CType(Me.SplitContainer, System.ComponentModel.ISupportInitialize).EndInit()
        Me.SplitContainer.ResumeLayout(False)
        CType(Me.dgvTable, System.ComponentModel.ISupportInitialize).EndInit()
        Me.Panel2.ResumeLayout(False)
        CType(Me.dgvList, System.ComponentModel.ISupportInitialize).EndInit()
        Me.GroupBox1.ResumeLayout(False)
        Me.GroupBox1.PerformLayout()
        Me.Panel3.ResumeLayout(False)
        Me.panelButton.ResumeLayout(False)
        Me.panelPagination.ResumeLayout(False)
        Me.panelPagination.PerformLayout()
        Me.panelTop.ResumeLayout(False)
        Me.Panel4.ResumeLayout(False)
        Me.Panel4.PerformLayout()
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents panelContainer As Panel
    Friend WithEvents panelTop As Panel
    Friend WithEvents btnExcludedEmployee As Button
    Friend WithEvents btnIncludedEmployee As Button
    Friend WithEvents panelButton As Panel
    Friend WithEvents panelPagination As Panel
    Friend WithEvents SplitContainer As SplitContainer
    Friend WithEvents Panel2 As Panel
    Friend WithEvents dgvTable As DataGridView
    Friend WithEvents DataGridViewImageColumn1 As DataGridViewImageColumn
    Friend WithEvents DataGridViewImageColumn2 As DataGridViewImageColumn
    Friend WithEvents dgvList As DataGridView
    Friend WithEvents GroupBox1 As GroupBox
    Friend WithEvents lblAssignedArea As Label
    Friend WithEvents lblDesignation As Label
    Friend WithEvents lblName As Label
    Friend WithEvents Panel3 As Panel
    Friend WithEvents btnClose As Button
    Friend WithEvents lblMessage As Label
    Friend WithEvents tmrSlide As Timer
    Friend WithEvents lblEmployeeID As Label
    Friend WithEvents record_row_number As DataGridViewTextBoxColumn
    Friend WithEvents record_id As DataGridViewTextBoxColumn
    Friend WithEvents record_type As DataGridViewTextBoxColumn
    Friend WithEvents record_value As DataGridViewTextBoxColumn
    Friend WithEvents row_number As DataGridViewTextBoxColumn
    Friend WithEvents employeeID As DataGridViewTextBoxColumn
    Friend WithEvents employee_number As DataGridViewTextBoxColumn
    Friend WithEvents employee_name As DataGridViewTextBoxColumn
    Friend WithEvents employee_designation As DataGridViewTextBoxColumn
    Friend WithEvents employee_assigned_area As DataGridViewTextBoxColumn
    Friend WithEvents employee_remarks As DataGridViewTextBoxColumn
    Friend WithEvents employee_status As DataGridViewTextBoxColumn
    Friend WithEvents action_view As DataGridViewButtonColumn
    Friend WithEvents action_exclude_or_inlucde As DataGridViewButtonColumn
    Friend WithEvents action_manage_deduction As DataGridViewButtonColumn
    Friend WithEvents action_manage_receivables As DataGridViewButtonColumn
    Friend WithEvents lblTitle As Label
    Friend WithEvents Panel4 As Panel
    Friend WithEvents cmbPerPage As ComboBox
    Friend WithEvents Label1 As Label
    Friend WithEvents lblPerPage As Label
    Friend WithEvents btnNext As Button
    Friend WithEvents lblPage As Label
    Friend WithEvents cmbPage As ComboBox
    Friend WithEvents btnPrevious As Button
End Class
