<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class UcManageDeductionGroup
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
        Dim DataGridViewCellStyle4 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Me.panelContainer = New System.Windows.Forms.Panel()
        Me.panelContent = New System.Windows.Forms.Panel()
        Me.dgvTable = New System.Windows.Forms.DataGridView()
        Me.row_number = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.deduction_group_id = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.deduction_group_name = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.deduction_group_code = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.action_edit = New System.Windows.Forms.DataGridViewButtonColumn()
        Me.action_delete = New System.Windows.Forms.DataGridViewButtonColumn()
        Me.panelNavigator = New System.Windows.Forms.Panel()
        Me.Panel1 = New System.Windows.Forms.Panel()
        Me.btnAdd = New System.Windows.Forms.Button()
        Me.txtSearch = New System.Windows.Forms.TextBox()
        Me.panelTop = New System.Windows.Forms.Panel()
        Me.lblDescription = New System.Windows.Forms.Label()
        Me.lblTitle = New System.Windows.Forms.Label()
        Me.panelContainer.SuspendLayout()
        Me.panelContent.SuspendLayout()
        CType(Me.dgvTable, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.panelNavigator.SuspendLayout()
        Me.Panel1.SuspendLayout()
        Me.panelTop.SuspendLayout()
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
        Me.panelContent.Controls.Add(Me.panelNavigator)
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
        Me.dgvTable.Columns.AddRange(New System.Windows.Forms.DataGridViewColumn() {Me.row_number, Me.deduction_group_id, Me.deduction_group_name, Me.deduction_group_code, Me.action_edit, Me.action_delete})
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
        Me.dgvTable.Location = New System.Drawing.Point(0, 67)
        Me.dgvTable.Name = "dgvTable"
        Me.dgvTable.ReadOnly = True
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
        Me.dgvTable.Size = New System.Drawing.Size(957, 395)
        Me.dgvTable.TabIndex = 4
        '
        'row_number
        '
        Me.row_number.FillWeight = 10.0!
        Me.row_number.HeaderText = "#"
        Me.row_number.Name = "row_number"
        Me.row_number.ReadOnly = True
        '
        'deduction_group_id
        '
        Me.deduction_group_id.HeaderText = "ID"
        Me.deduction_group_id.Name = "deduction_group_id"
        Me.deduction_group_id.ReadOnly = True
        Me.deduction_group_id.Visible = False
        '
        'deduction_group_name
        '
        Me.deduction_group_name.HeaderText = "Name"
        Me.deduction_group_name.Name = "deduction_group_name"
        Me.deduction_group_name.ReadOnly = True
        '
        'deduction_group_code
        '
        Me.deduction_group_code.HeaderText = "Code"
        Me.deduction_group_code.Name = "deduction_group_code"
        Me.deduction_group_code.ReadOnly = True
        '
        'action_edit
        '
        Me.action_edit.HeaderText = ""
        Me.action_edit.Name = "action_edit"
        Me.action_edit.ReadOnly = True
        '
        'action_delete
        '
        Me.action_delete.HeaderText = ""
        Me.action_delete.Name = "action_delete"
        Me.action_delete.ReadOnly = True
        '
        'panelNavigator
        '
        Me.panelNavigator.BackColor = System.Drawing.Color.WhiteSmoke
        Me.panelNavigator.Controls.Add(Me.Panel1)
        Me.panelNavigator.Dock = System.Windows.Forms.DockStyle.Top
        Me.panelNavigator.Location = New System.Drawing.Point(0, 0)
        Me.panelNavigator.Name = "panelNavigator"
        Me.panelNavigator.Size = New System.Drawing.Size(957, 67)
        Me.panelNavigator.TabIndex = 3
        '
        'Panel1
        '
        Me.Panel1.Anchor = CType((((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom) _
            Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.Panel1.BackColor = System.Drawing.Color.White
        Me.Panel1.Controls.Add(Me.btnAdd)
        Me.Panel1.Controls.Add(Me.txtSearch)
        Me.Panel1.Location = New System.Drawing.Point(13, 10)
        Me.Panel1.Name = "Panel1"
        Me.Panel1.Size = New System.Drawing.Size(932, 46)
        Me.Panel1.TabIndex = 0
        '
        'btnAdd
        '
        Me.btnAdd.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnAdd.BackColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.btnAdd.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnAdd.Font = New System.Drawing.Font("Segoe UI Semibold", 9.75!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnAdd.ForeColor = System.Drawing.Color.White
        Me.btnAdd.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnAdd.Location = New System.Drawing.Point(791, 9)
        Me.btnAdd.Name = "btnAdd"
        Me.btnAdd.Size = New System.Drawing.Size(138, 29)
        Me.btnAdd.TabIndex = 42
        Me.btnAdd.Text = "ADD"
        Me.btnAdd.TextImageRelation = System.Windows.Forms.TextImageRelation.ImageBeforeText
        Me.btnAdd.UseVisualStyleBackColor = False
        '
        'txtSearch
        '
        Me.txtSearch.BackColor = System.Drawing.Color.White
        Me.txtSearch.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle
        Me.txtSearch.Font = New System.Drawing.Font("Segoe UI Semibold", 12.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.txtSearch.ForeColor = System.Drawing.Color.Gray
        Me.txtSearch.Location = New System.Drawing.Point(7, 9)
        Me.txtSearch.Name = "txtSearch"
        Me.txtSearch.Size = New System.Drawing.Size(186, 29)
        Me.txtSearch.TabIndex = 41
        Me.txtSearch.Text = "Search"
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
        Me.lblDescription.Size = New System.Drawing.Size(567, 19)
        Me.lblDescription.TabIndex = 3
        Me.lblDescription.Text = "Generate different types of reports here to suit your-data-intensive workflow req" &
    "uirements."
        '
        'lblTitle
        '
        Me.lblTitle.AutoSize = True
        Me.lblTitle.Font = New System.Drawing.Font("Segoe UI", 12.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblTitle.ForeColor = System.Drawing.Color.White
        Me.lblTitle.Location = New System.Drawing.Point(16, 12)
        Me.lblTitle.Name = "lblTitle"
        Me.lblTitle.Size = New System.Drawing.Size(141, 21)
        Me.lblTitle.TabIndex = 2
        Me.lblTitle.Text = "Deduction Group"
        Me.lblTitle.TextAlign = System.Drawing.ContentAlignment.MiddleCenter
        '
        'UcManageDeductionGroup
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.White
        Me.Controls.Add(Me.panelContainer)
        Me.Name = "UcManageDeductionGroup"
        Me.Size = New System.Drawing.Size(957, 528)
        Me.panelContainer.ResumeLayout(False)
        Me.panelContent.ResumeLayout(False)
        CType(Me.dgvTable, System.ComponentModel.ISupportInitialize).EndInit()
        Me.panelNavigator.ResumeLayout(False)
        Me.Panel1.ResumeLayout(False)
        Me.Panel1.PerformLayout()
        Me.panelTop.ResumeLayout(False)
        Me.panelTop.PerformLayout()
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents panelContainer As Panel
    Friend WithEvents panelContent As Panel
    Friend WithEvents panelTop As Panel
    Friend WithEvents lblDescription As Label
    Friend WithEvents lblTitle As Label
    Friend WithEvents panelNavigator As Panel
    Friend WithEvents Panel1 As Panel
    Friend WithEvents dgvTable As DataGridView
    Friend WithEvents row_number As DataGridViewTextBoxColumn
    Friend WithEvents deduction_group_id As DataGridViewTextBoxColumn
    Friend WithEvents deduction_group_name As DataGridViewTextBoxColumn
    Friend WithEvents deduction_group_code As DataGridViewTextBoxColumn
    Friend WithEvents action_edit As DataGridViewButtonColumn
    Friend WithEvents action_delete As DataGridViewButtonColumn
    Friend WithEvents txtSearch As TextBox
    Friend WithEvents btnAdd As Button
End Class
