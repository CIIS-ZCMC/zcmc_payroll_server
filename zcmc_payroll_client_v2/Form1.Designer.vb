Imports MaterialSkin.Controls

<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()>
Partial Class Form1
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
        Me.components = New System.ComponentModel.Container()
        Dim DataGridViewCellStyle5 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle6 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle7 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle8 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Me.tmrSlide = New System.Windows.Forms.Timer(Me.components)
        Me.SplitContainer1 = New System.Windows.Forms.SplitContainer()
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
        Me.btnClose = New System.Windows.Forms.Button()
        Me.lblDepartment = New System.Windows.Forms.Label()
        Me.lblName = New System.Windows.Forms.Label()
        Me.lblId = New System.Windows.Forms.Label()
        CType(Me.SplitContainer1, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.SplitContainer1.Panel1.SuspendLayout()
        Me.SplitContainer1.Panel2.SuspendLayout()
        Me.SplitContainer1.SuspendLayout()
        CType(Me.dgvTable, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.SuspendLayout()
        '
        'tmrSlide
        '
        Me.tmrSlide.Interval = 10
        '
        'SplitContainer1
        '
        Me.SplitContainer1.Dock = System.Windows.Forms.DockStyle.Fill
        Me.SplitContainer1.Location = New System.Drawing.Point(0, 0)
        Me.SplitContainer1.Name = "SplitContainer1"
        '
        'SplitContainer1.Panel1
        '
        Me.SplitContainer1.Panel1.Controls.Add(Me.dgvTable)
        '
        'SplitContainer1.Panel2
        '
        Me.SplitContainer1.Panel2.Controls.Add(Me.btnClose)
        Me.SplitContainer1.Panel2.Controls.Add(Me.lblDepartment)
        Me.SplitContainer1.Panel2.Controls.Add(Me.lblName)
        Me.SplitContainer1.Panel2.Controls.Add(Me.lblId)
        Me.SplitContainer1.Size = New System.Drawing.Size(1050, 590)
        Me.SplitContainer1.SplitterDistance = 809
        Me.SplitContainer1.TabIndex = 0
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
        DataGridViewCellStyle5.BackColor = System.Drawing.Color.SteelBlue
        DataGridViewCellStyle5.Font = New System.Drawing.Font("Tahoma", 14.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        DataGridViewCellStyle5.ForeColor = System.Drawing.Color.White
        DataGridViewCellStyle5.SelectionBackColor = System.Drawing.Color.SteelBlue
        DataGridViewCellStyle5.SelectionForeColor = System.Drawing.Color.White
        DataGridViewCellStyle5.WrapMode = System.Windows.Forms.DataGridViewTriState.[True]
        Me.dgvTable.ColumnHeadersDefaultCellStyle = DataGridViewCellStyle5
        Me.dgvTable.ColumnHeadersHeightSizeMode = System.Windows.Forms.DataGridViewColumnHeadersHeightSizeMode.AutoSize
        Me.dgvTable.Columns.AddRange(New System.Windows.Forms.DataGridViewColumn() {Me.row_number, Me.employeeID, Me.employee_number, Me.employee_name, Me.employee_designation, Me.employee_assigned_area, Me.employee_remarks, Me.employee_status, Me.action_view, Me.action_exclude_or_inlucde, Me.action_manage_deduction, Me.action_manage_receivables})
        DataGridViewCellStyle6.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleLeft
        DataGridViewCellStyle6.BackColor = System.Drawing.Color.White
        DataGridViewCellStyle6.Font = New System.Drawing.Font("Tahoma", 9.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        DataGridViewCellStyle6.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        DataGridViewCellStyle6.SelectionBackColor = System.Drawing.Color.Gainsboro
        DataGridViewCellStyle6.SelectionForeColor = System.Drawing.Color.Black
        DataGridViewCellStyle6.WrapMode = System.Windows.Forms.DataGridViewTriState.[False]
        Me.dgvTable.DefaultCellStyle = DataGridViewCellStyle6
        Me.dgvTable.Dock = System.Windows.Forms.DockStyle.Fill
        Me.dgvTable.EnableHeadersVisualStyles = False
        Me.dgvTable.GridColor = System.Drawing.Color.WhiteSmoke
        Me.dgvTable.Location = New System.Drawing.Point(0, 0)
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
        Me.dgvTable.Size = New System.Drawing.Size(809, 590)
        Me.dgvTable.TabIndex = 2
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
        'btnClose
        '
        Me.btnClose.Dock = System.Windows.Forms.DockStyle.Top
        Me.btnClose.Location = New System.Drawing.Point(0, 0)
        Me.btnClose.Name = "btnClose"
        Me.btnClose.Size = New System.Drawing.Size(237, 23)
        Me.btnClose.TabIndex = 3
        Me.btnClose.Text = "X"
        Me.btnClose.UseVisualStyleBackColor = True
        '
        'lblDepartment
        '
        Me.lblDepartment.AutoSize = True
        Me.lblDepartment.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblDepartment.Location = New System.Drawing.Point(20, 86)
        Me.lblDepartment.Name = "lblDepartment"
        Me.lblDepartment.Size = New System.Drawing.Size(66, 24)
        Me.lblDepartment.TabIndex = 2
        Me.lblDepartment.Text = "Label3"
        '
        'lblName
        '
        Me.lblName.AutoSize = True
        Me.lblName.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblName.Location = New System.Drawing.Point(20, 62)
        Me.lblName.Name = "lblName"
        Me.lblName.Size = New System.Drawing.Size(66, 24)
        Me.lblName.TabIndex = 1
        Me.lblName.Text = "Label2"
        '
        'lblId
        '
        Me.lblId.AutoSize = True
        Me.lblId.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblId.Location = New System.Drawing.Point(20, 38)
        Me.lblId.Name = "lblId"
        Me.lblId.Size = New System.Drawing.Size(66, 24)
        Me.lblId.TabIndex = 0
        Me.lblId.Text = "Label1"
        '
        'Form1
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.White
        Me.ClientSize = New System.Drawing.Size(1050, 590)
        Me.Controls.Add(Me.SplitContainer1)
        Me.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        Me.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedSingle
        Me.Name = "Form1"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen
        Me.WindowState = System.Windows.Forms.FormWindowState.Maximized
        Me.SplitContainer1.Panel1.ResumeLayout(False)
        Me.SplitContainer1.Panel2.ResumeLayout(False)
        Me.SplitContainer1.Panel2.PerformLayout()
        CType(Me.SplitContainer1, System.ComponentModel.ISupportInitialize).EndInit()
        Me.SplitContainer1.ResumeLayout(False)
        CType(Me.dgvTable, System.ComponentModel.ISupportInitialize).EndInit()
        Me.ResumeLayout(False)

    End Sub
    Friend WithEvents tmrSlide As Timer
    Friend WithEvents SplitContainer1 As SplitContainer
    Friend WithEvents dgvTable As DataGridView
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
    Friend WithEvents lblDepartment As Label
    Friend WithEvents lblName As Label
    Friend WithEvents lblId As Label
    Friend WithEvents btnClose As Button
End Class
