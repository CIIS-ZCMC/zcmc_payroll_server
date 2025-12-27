<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()>
Partial Class UcEmployeeRefetch
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
        Dim DataGridViewCellStyle5 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle6 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle7 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle8 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Me.panelContainer = New System.Windows.Forms.Panel()
        Me.panelContent = New System.Windows.Forms.Panel()
        Me.panelNavigator = New System.Windows.Forms.Panel()
        Me.pnlNavigator = New System.Windows.Forms.Panel()
        Me.btnFetchSelectedUser = New System.Windows.Forms.Button()
        Me.btnSelectAll = New System.Windows.Forms.Button()
        Me.panelTop = New System.Windows.Forms.Panel()
        Me.lblDescription = New System.Windows.Forms.Label()
        Me.lblTitle = New System.Windows.Forms.Label()
        Me.lstbxSelectedEmployee = New System.Windows.Forms.ListBox()
        Me.dgvTable = New System.Windows.Forms.DataGridView()
        Me.row_number = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.selected_row = New System.Windows.Forms.DataGridViewCheckBoxColumn()
        Me.employeeID = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_number = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_name = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_designation = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_assigned_area = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_remarks = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.employee_status = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.panelContainer.SuspendLayout()
        Me.panelContent.SuspendLayout()
        Me.panelNavigator.SuspendLayout()
        Me.pnlNavigator.SuspendLayout()
        Me.panelTop.SuspendLayout()
        CType(Me.dgvTable, System.ComponentModel.ISupportInitialize).BeginInit()
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
        Me.panelContainer.TabIndex = 3
        '
        'panelContent
        '
        Me.panelContent.Controls.Add(Me.dgvTable)
        Me.panelContent.Controls.Add(Me.lstbxSelectedEmployee)
        Me.panelContent.Controls.Add(Me.panelNavigator)
        Me.panelContent.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContent.Location = New System.Drawing.Point(0, 66)
        Me.panelContent.Name = "panelContent"
        Me.panelContent.Size = New System.Drawing.Size(957, 462)
        Me.panelContent.TabIndex = 33
        '
        'panelNavigator
        '
        Me.panelNavigator.BackColor = System.Drawing.Color.WhiteSmoke
        Me.panelNavigator.Controls.Add(Me.pnlNavigator)
        Me.panelNavigator.Dock = System.Windows.Forms.DockStyle.Top
        Me.panelNavigator.Location = New System.Drawing.Point(0, 0)
        Me.panelNavigator.Name = "panelNavigator"
        Me.panelNavigator.Size = New System.Drawing.Size(957, 67)
        Me.panelNavigator.TabIndex = 3
        '
        'pnlNavigator
        '
        Me.pnlNavigator.Anchor = CType((((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom) _
            Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.pnlNavigator.BackColor = System.Drawing.Color.White
        Me.pnlNavigator.Controls.Add(Me.btnFetchSelectedUser)
        Me.pnlNavigator.Controls.Add(Me.btnSelectAll)
        Me.pnlNavigator.Location = New System.Drawing.Point(20, 10)
        Me.pnlNavigator.Name = "pnlNavigator"
        Me.pnlNavigator.Size = New System.Drawing.Size(925, 46)
        Me.pnlNavigator.TabIndex = 0
        '
        'btnFetchSelectedUser
        '
        Me.btnFetchSelectedUser.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnFetchSelectedUser.BackColor = System.Drawing.Color.White
        Me.btnFetchSelectedUser.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnFetchSelectedUser.Font = New System.Drawing.Font("Segoe UI Semibold", 11.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnFetchSelectedUser.ForeColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.btnFetchSelectedUser.Location = New System.Drawing.Point(708, 8)
        Me.btnFetchSelectedUser.Name = "btnFetchSelectedUser"
        Me.btnFetchSelectedUser.Size = New System.Drawing.Size(203, 31)
        Me.btnFetchSelectedUser.TabIndex = 42
        Me.btnFetchSelectedUser.Text = "Fetch Selected Employee"
        Me.btnFetchSelectedUser.UseVisualStyleBackColor = False
        '
        'btnSelectAll
        '
        Me.btnSelectAll.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnSelectAll.BackColor = System.Drawing.Color.White
        Me.btnSelectAll.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnSelectAll.Font = New System.Drawing.Font("Segoe UI Semibold", 11.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnSelectAll.ForeColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.btnSelectAll.Location = New System.Drawing.Point(600, 8)
        Me.btnSelectAll.Name = "btnSelectAll"
        Me.btnSelectAll.Size = New System.Drawing.Size(102, 31)
        Me.btnSelectAll.TabIndex = 41
        Me.btnSelectAll.Text = "Fetch All"
        Me.btnSelectAll.UseVisualStyleBackColor = False
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
        Me.lblDescription.AutoSize = True
        Me.lblDescription.Font = New System.Drawing.Font("Segoe UI", 10.0!)
        Me.lblDescription.ForeColor = System.Drawing.Color.White
        Me.lblDescription.Location = New System.Drawing.Point(16, 32)
        Me.lblDescription.Name = "lblDescription"
        Me.lblDescription.Size = New System.Drawing.Size(202, 19)
        Me.lblDescription.TabIndex = 3
        Me.lblDescription.Text = "Update Employee Time Records"
        '
        'lblTitle
        '
        Me.lblTitle.AutoSize = True
        Me.lblTitle.Font = New System.Drawing.Font("Segoe UI", 12.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblTitle.ForeColor = System.Drawing.Color.White
        Me.lblTitle.Location = New System.Drawing.Point(16, 12)
        Me.lblTitle.Name = "lblTitle"
        Me.lblTitle.Size = New System.Drawing.Size(220, 21)
        Me.lblTitle.TabIndex = 2
        Me.lblTitle.Text = "Re-Fetch Employee Records"
        Me.lblTitle.TextAlign = System.Drawing.ContentAlignment.MiddleCenter
        '
        'lstbxSelectedEmployee
        '
        Me.lstbxSelectedEmployee.Dock = System.Windows.Forms.DockStyle.Right
        Me.lstbxSelectedEmployee.FormattingEnabled = True
        Me.lstbxSelectedEmployee.Location = New System.Drawing.Point(761, 67)
        Me.lstbxSelectedEmployee.Name = "lstbxSelectedEmployee"
        Me.lstbxSelectedEmployee.Size = New System.Drawing.Size(196, 395)
        Me.lstbxSelectedEmployee.TabIndex = 4
        '
        'dgvTable
        '
        Me.dgvTable.AllowUserToAddRows = False
        Me.dgvTable.AllowUserToDeleteRows = False
        Me.dgvTable.AutoSizeColumnsMode = System.Windows.Forms.DataGridViewAutoSizeColumnsMode.Fill
        Me.dgvTable.BackgroundColor = System.Drawing.Color.White
        Me.dgvTable.BorderStyle = System.Windows.Forms.BorderStyle.None
        Me.dgvTable.ColumnHeadersBorderStyle = System.Windows.Forms.DataGridViewHeaderBorderStyle.None
        DataGridViewCellStyle5.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleLeft
        DataGridViewCellStyle5.BackColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        DataGridViewCellStyle5.Font = New System.Drawing.Font("Tahoma", 14.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        DataGridViewCellStyle5.ForeColor = System.Drawing.Color.White
        DataGridViewCellStyle5.SelectionBackColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        DataGridViewCellStyle5.SelectionForeColor = System.Drawing.Color.White
        DataGridViewCellStyle5.WrapMode = System.Windows.Forms.DataGridViewTriState.[True]
        Me.dgvTable.ColumnHeadersDefaultCellStyle = DataGridViewCellStyle5
        Me.dgvTable.ColumnHeadersHeightSizeMode = System.Windows.Forms.DataGridViewColumnHeadersHeightSizeMode.AutoSize
        Me.dgvTable.Columns.AddRange(New System.Windows.Forms.DataGridViewColumn() {Me.row_number, Me.selected_row, Me.employeeID, Me.employee_number, Me.employee_name, Me.employee_designation, Me.employee_assigned_area, Me.employee_remarks, Me.employee_status})
        DataGridViewCellStyle6.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleLeft
        DataGridViewCellStyle6.BackColor = System.Drawing.Color.White
        DataGridViewCellStyle6.Font = New System.Drawing.Font("Tahoma", 9.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        DataGridViewCellStyle6.ForeColor = System.Drawing.SystemColors.ControlText
        DataGridViewCellStyle6.SelectionBackColor = System.Drawing.Color.Gainsboro
        DataGridViewCellStyle6.SelectionForeColor = System.Drawing.Color.Black
        DataGridViewCellStyle6.WrapMode = System.Windows.Forms.DataGridViewTriState.[False]
        Me.dgvTable.DefaultCellStyle = DataGridViewCellStyle6
        Me.dgvTable.Dock = System.Windows.Forms.DockStyle.Fill
        Me.dgvTable.EnableHeadersVisualStyles = False
        Me.dgvTable.GridColor = System.Drawing.Color.WhiteSmoke
        Me.dgvTable.Location = New System.Drawing.Point(0, 67)
        Me.dgvTable.Name = "dgvTable"
        Me.dgvTable.ReadOnly = True
        DataGridViewCellStyle7.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleLeft
        DataGridViewCellStyle7.BackColor = System.Drawing.Color.White
        DataGridViewCellStyle7.Font = New System.Drawing.Font("Microsoft Sans Serif", 8.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        DataGridViewCellStyle7.ForeColor = System.Drawing.Color.Black
        DataGridViewCellStyle7.SelectionBackColor = System.Drawing.Color.SteelBlue
        DataGridViewCellStyle7.SelectionForeColor = System.Drawing.SystemColors.HighlightText
        DataGridViewCellStyle7.WrapMode = System.Windows.Forms.DataGridViewTriState.[True]
        Me.dgvTable.RowHeadersDefaultCellStyle = DataGridViewCellStyle7
        Me.dgvTable.RowHeadersVisible = False
        DataGridViewCellStyle8.Font = New System.Drawing.Font("Tahoma", 11.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.dgvTable.RowsDefaultCellStyle = DataGridViewCellStyle8
        Me.dgvTable.RowTemplate.Height = 26
        Me.dgvTable.SelectionMode = System.Windows.Forms.DataGridViewSelectionMode.FullRowSelect
        Me.dgvTable.Size = New System.Drawing.Size(761, 395)
        Me.dgvTable.TabIndex = 5
        '
        'row_number
        '
        Me.row_number.FillWeight = 10.0!
        Me.row_number.HeaderText = "#"
        Me.row_number.Name = "row_number"
        Me.row_number.ReadOnly = True
        '
        'selected_row
        '
        Me.selected_row.FillWeight = 20.0!
        Me.selected_row.HeaderText = ""
        Me.selected_row.Name = "selected_row"
        Me.selected_row.ReadOnly = True
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
        'UcEmployeeRefetch
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.White
        Me.Controls.Add(Me.panelContainer)
        Me.Name = "UcEmployeeRefetch"
        Me.Size = New System.Drawing.Size(957, 528)
        Me.panelContainer.ResumeLayout(False)
        Me.panelContent.ResumeLayout(False)
        Me.panelNavigator.ResumeLayout(False)
        Me.pnlNavigator.ResumeLayout(False)
        Me.panelTop.ResumeLayout(False)
        Me.panelTop.PerformLayout()
        CType(Me.dgvTable, System.ComponentModel.ISupportInitialize).EndInit()
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents panelContainer As Panel
    Friend WithEvents panelContent As Panel
    Friend WithEvents panelTop As Panel
    Friend WithEvents lblDescription As Label
    Friend WithEvents lblTitle As Label
    Friend WithEvents panelNavigator As Panel
    Friend WithEvents pnlNavigator As Panel
    Friend WithEvents btnFetchSelectedUser As Button
    Friend WithEvents btnSelectAll As Button
    Friend WithEvents dgvTable As DataGridView
    Friend WithEvents row_number As DataGridViewTextBoxColumn
    Friend WithEvents selected_row As DataGridViewCheckBoxColumn
    Friend WithEvents employeeID As DataGridViewTextBoxColumn
    Friend WithEvents employee_number As DataGridViewTextBoxColumn
    Friend WithEvents employee_name As DataGridViewTextBoxColumn
    Friend WithEvents employee_designation As DataGridViewTextBoxColumn
    Friend WithEvents employee_assigned_area As DataGridViewTextBoxColumn
    Friend WithEvents employee_remarks As DataGridViewTextBoxColumn
    Friend WithEvents employee_status As DataGridViewTextBoxColumn
    Friend WithEvents lstbxSelectedEmployee As ListBox
End Class
