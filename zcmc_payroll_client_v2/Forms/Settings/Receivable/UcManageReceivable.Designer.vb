<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class UcManageReceivable
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
        Dim DataGridViewCellStyle13 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle14 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle15 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Dim DataGridViewCellStyle16 As System.Windows.Forms.DataGridViewCellStyle = New System.Windows.Forms.DataGridViewCellStyle()
        Me.panelContainer = New System.Windows.Forms.Panel()
        Me.panelContent = New System.Windows.Forms.Panel()
        Me.dgvTable = New System.Windows.Forms.DataGridView()
        Me.row_number = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.receivable_id = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.receivable_name = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.receivable_code = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.receivable_status = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.receivable_is_mandatory = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.receivable_date_updated = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.receivable_date_created = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.receivable_date_stopped = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.receivable_remarks = New System.Windows.Forms.DataGridViewTextBoxColumn()
        Me.action_edit = New System.Windows.Forms.DataGridViewButtonColumn()
        Me.action_delete = New System.Windows.Forms.DataGridViewButtonColumn()
        Me.action_resume = New System.Windows.Forms.DataGridViewButtonColumn()
        Me.action_manage_rule = New System.Windows.Forms.DataGridViewButtonColumn()
        Me.panelNavigator = New System.Windows.Forms.Panel()
        Me.pnlNavigator = New System.Windows.Forms.Panel()
        Me.btnAdd = New System.Windows.Forms.Button()
        Me.txtSearch = New System.Windows.Forms.TextBox()
        Me.panelTop = New System.Windows.Forms.Panel()
        Me.lblDescription = New System.Windows.Forms.Label()
        Me.lblTitle = New System.Windows.Forms.Label()
        Me.panelContainer.SuspendLayout()
        Me.panelContent.SuspendLayout()
        CType(Me.dgvTable, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.panelNavigator.SuspendLayout()
        Me.pnlNavigator.SuspendLayout()
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
        Me.panelContainer.TabIndex = 4
        '
        'panelContent
        '
        Me.panelContent.Controls.Add(Me.dgvTable)
        Me.panelContent.Controls.Add(Me.panelNavigator)
        Me.panelContent.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContent.Font = New System.Drawing.Font("Segoe UI Semibold", 11.25!, System.Drawing.FontStyle.Bold)
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
        DataGridViewCellStyle13.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleLeft
        DataGridViewCellStyle13.BackColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        DataGridViewCellStyle13.Font = New System.Drawing.Font("Segoe UI Semibold", 11.25!, System.Drawing.FontStyle.Bold)
        DataGridViewCellStyle13.ForeColor = System.Drawing.Color.White
        DataGridViewCellStyle13.SelectionBackColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        DataGridViewCellStyle13.SelectionForeColor = System.Drawing.Color.White
        DataGridViewCellStyle13.WrapMode = System.Windows.Forms.DataGridViewTriState.[True]
        Me.dgvTable.ColumnHeadersDefaultCellStyle = DataGridViewCellStyle13
        Me.dgvTable.ColumnHeadersHeightSizeMode = System.Windows.Forms.DataGridViewColumnHeadersHeightSizeMode.AutoSize
        Me.dgvTable.Columns.AddRange(New System.Windows.Forms.DataGridViewColumn() {Me.row_number, Me.receivable_id, Me.receivable_name, Me.receivable_code, Me.receivable_status, Me.receivable_is_mandatory, Me.receivable_date_updated, Me.receivable_date_created, Me.receivable_date_stopped, Me.receivable_remarks, Me.action_edit, Me.action_delete, Me.action_resume, Me.action_manage_rule})
        DataGridViewCellStyle14.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleLeft
        DataGridViewCellStyle14.BackColor = System.Drawing.Color.White
        DataGridViewCellStyle14.Font = New System.Drawing.Font("Segoe UI Semibold", 11.25!, System.Drawing.FontStyle.Bold)
        DataGridViewCellStyle14.ForeColor = System.Drawing.SystemColors.ControlText
        DataGridViewCellStyle14.SelectionBackColor = System.Drawing.Color.Gainsboro
        DataGridViewCellStyle14.SelectionForeColor = System.Drawing.Color.Black
        DataGridViewCellStyle14.WrapMode = System.Windows.Forms.DataGridViewTriState.[False]
        Me.dgvTable.DefaultCellStyle = DataGridViewCellStyle14
        Me.dgvTable.Dock = System.Windows.Forms.DockStyle.Fill
        Me.dgvTable.EnableHeadersVisualStyles = False
        Me.dgvTable.GridColor = System.Drawing.Color.WhiteSmoke
        Me.dgvTable.Location = New System.Drawing.Point(0, 67)
        Me.dgvTable.Name = "dgvTable"
        Me.dgvTable.ReadOnly = True
        DataGridViewCellStyle15.Alignment = System.Windows.Forms.DataGridViewContentAlignment.MiddleLeft
        DataGridViewCellStyle15.BackColor = System.Drawing.Color.White
        DataGridViewCellStyle15.Font = New System.Drawing.Font("Segoe UI Semibold", 11.25!, System.Drawing.FontStyle.Bold)
        DataGridViewCellStyle15.ForeColor = System.Drawing.Color.Black
        DataGridViewCellStyle15.SelectionBackColor = System.Drawing.Color.SteelBlue
        DataGridViewCellStyle15.SelectionForeColor = System.Drawing.SystemColors.HighlightText
        DataGridViewCellStyle15.WrapMode = System.Windows.Forms.DataGridViewTriState.[True]
        Me.dgvTable.RowHeadersDefaultCellStyle = DataGridViewCellStyle15
        Me.dgvTable.RowHeadersVisible = False
        DataGridViewCellStyle16.Font = New System.Drawing.Font("Tahoma", 11.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.dgvTable.RowsDefaultCellStyle = DataGridViewCellStyle16
        Me.dgvTable.RowTemplate.Height = 26
        Me.dgvTable.SelectionMode = System.Windows.Forms.DataGridViewSelectionMode.FullRowSelect
        Me.dgvTable.Size = New System.Drawing.Size(957, 395)
        Me.dgvTable.TabIndex = 8
        '
        'row_number
        '
        Me.row_number.FillWeight = 10.0!
        Me.row_number.HeaderText = "#"
        Me.row_number.Name = "row_number"
        Me.row_number.ReadOnly = True
        '
        'receivable_id
        '
        Me.receivable_id.HeaderText = "ID"
        Me.receivable_id.Name = "receivable_id"
        Me.receivable_id.ReadOnly = True
        Me.receivable_id.Visible = False
        '
        'receivable_name
        '
        Me.receivable_name.HeaderText = "Name"
        Me.receivable_name.Name = "receivable_name"
        Me.receivable_name.ReadOnly = True
        '
        'receivable_code
        '
        Me.receivable_code.HeaderText = "Code"
        Me.receivable_code.Name = "receivable_code"
        Me.receivable_code.ReadOnly = True
        '
        'receivable_status
        '
        Me.receivable_status.HeaderText = "Status"
        Me.receivable_status.Name = "receivable_status"
        Me.receivable_status.ReadOnly = True
        '
        'receivable_is_mandatory
        '
        Me.receivable_is_mandatory.HeaderText = "Is Mandatory"
        Me.receivable_is_mandatory.Name = "receivable_is_mandatory"
        Me.receivable_is_mandatory.ReadOnly = True
        '
        'receivable_date_updated
        '
        Me.receivable_date_updated.HeaderText = "Date Updated"
        Me.receivable_date_updated.Name = "receivable_date_updated"
        Me.receivable_date_updated.ReadOnly = True
        '
        'receivable_date_created
        '
        Me.receivable_date_created.HeaderText = "Date Created"
        Me.receivable_date_created.Name = "receivable_date_created"
        Me.receivable_date_created.ReadOnly = True
        '
        'receivable_date_stopped
        '
        Me.receivable_date_stopped.HeaderText = "Date Stopped"
        Me.receivable_date_stopped.Name = "receivable_date_stopped"
        Me.receivable_date_stopped.ReadOnly = True
        '
        'receivable_remarks
        '
        Me.receivable_remarks.HeaderText = "Remarks"
        Me.receivable_remarks.Name = "receivable_remarks"
        Me.receivable_remarks.ReadOnly = True
        '
        'action_edit
        '
        Me.action_edit.FillWeight = 40.0!
        Me.action_edit.HeaderText = ""
        Me.action_edit.Name = "action_edit"
        Me.action_edit.ReadOnly = True
        '
        'action_delete
        '
        Me.action_delete.FillWeight = 40.0!
        Me.action_delete.HeaderText = ""
        Me.action_delete.Name = "action_delete"
        Me.action_delete.ReadOnly = True
        '
        'action_resume
        '
        Me.action_resume.FillWeight = 40.0!
        Me.action_resume.HeaderText = ""
        Me.action_resume.Name = "action_resume"
        Me.action_resume.ReadOnly = True
        '
        'action_manage_rule
        '
        Me.action_manage_rule.FillWeight = 40.0!
        Me.action_manage_rule.HeaderText = ""
        Me.action_manage_rule.Name = "action_manage_rule"
        Me.action_manage_rule.ReadOnly = True
        '
        'panelNavigator
        '
        Me.panelNavigator.BackColor = System.Drawing.Color.WhiteSmoke
        Me.panelNavigator.Controls.Add(Me.pnlNavigator)
        Me.panelNavigator.Dock = System.Windows.Forms.DockStyle.Top
        Me.panelNavigator.Location = New System.Drawing.Point(0, 0)
        Me.panelNavigator.Name = "panelNavigator"
        Me.panelNavigator.Size = New System.Drawing.Size(957, 67)
        Me.panelNavigator.TabIndex = 7
        '
        'pnlNavigator
        '
        Me.pnlNavigator.Anchor = CType((System.Windows.Forms.AnchorStyles.Left Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.pnlNavigator.BackColor = System.Drawing.Color.White
        Me.pnlNavigator.Controls.Add(Me.btnAdd)
        Me.pnlNavigator.Controls.Add(Me.txtSearch)
        Me.pnlNavigator.Font = New System.Drawing.Font("Segoe UI Semibold", 9.75!, System.Drawing.FontStyle.Bold)
        Me.pnlNavigator.Location = New System.Drawing.Point(20, 15)
        Me.pnlNavigator.Name = "pnlNavigator"
        Me.pnlNavigator.Size = New System.Drawing.Size(921, 46)
        Me.pnlNavigator.TabIndex = 0
        '
        'btnAdd
        '
        Me.btnAdd.Anchor = System.Windows.Forms.AnchorStyles.Right
        Me.btnAdd.BackColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.btnAdd.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnAdd.Font = New System.Drawing.Font("Segoe UI Semibold", 11.25!, System.Drawing.FontStyle.Bold)
        Me.btnAdd.ForeColor = System.Drawing.Color.White
        Me.btnAdd.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnAdd.Location = New System.Drawing.Point(780, 10)
        Me.btnAdd.Name = "btnAdd"
        Me.btnAdd.Size = New System.Drawing.Size(138, 27)
        Me.btnAdd.TabIndex = 42
        Me.btnAdd.Text = "ADD"
        Me.btnAdd.TextImageRelation = System.Windows.Forms.TextImageRelation.ImageBeforeText
        Me.btnAdd.UseVisualStyleBackColor = True
        '
        'txtSearch
        '
        Me.txtSearch.BackColor = System.Drawing.Color.White
        Me.txtSearch.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle
        Me.txtSearch.Font = New System.Drawing.Font("Segoe UI Semibold", 11.25!, System.Drawing.FontStyle.Bold)
        Me.txtSearch.ForeColor = System.Drawing.Color.Gray
        Me.txtSearch.Location = New System.Drawing.Point(7, 10)
        Me.txtSearch.Name = "txtSearch"
        Me.txtSearch.Size = New System.Drawing.Size(186, 27)
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
        Me.lblTitle.Size = New System.Drawing.Size(100, 21)
        Me.lblTitle.TabIndex = 2
        Me.lblTitle.Text = "Receivables"
        Me.lblTitle.TextAlign = System.Drawing.ContentAlignment.MiddleCenter
        '
        'UcManageReceivable
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.White
        Me.Controls.Add(Me.panelContainer)
        Me.Name = "UcManageReceivable"
        Me.Size = New System.Drawing.Size(957, 528)
        Me.panelContainer.ResumeLayout(False)
        Me.panelContent.ResumeLayout(False)
        CType(Me.dgvTable, System.ComponentModel.ISupportInitialize).EndInit()
        Me.panelNavigator.ResumeLayout(False)
        Me.pnlNavigator.ResumeLayout(False)
        Me.pnlNavigator.PerformLayout()
        Me.panelTop.ResumeLayout(False)
        Me.panelTop.PerformLayout()
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents panelContainer As Panel
    Friend WithEvents panelContent As Panel
    Friend WithEvents panelTop As Panel
    Friend WithEvents lblDescription As Label
    Friend WithEvents lblTitle As Label
    Friend WithEvents dgvTable As DataGridView
    Friend WithEvents panelNavigator As Panel
    Friend WithEvents row_number As DataGridViewTextBoxColumn
    Friend WithEvents receivable_id As DataGridViewTextBoxColumn
    Friend WithEvents receivable_name As DataGridViewTextBoxColumn
    Friend WithEvents receivable_code As DataGridViewTextBoxColumn
    Friend WithEvents receivable_status As DataGridViewTextBoxColumn
    Friend WithEvents receivable_is_mandatory As DataGridViewTextBoxColumn
    Friend WithEvents receivable_date_updated As DataGridViewTextBoxColumn
    Friend WithEvents receivable_date_created As DataGridViewTextBoxColumn
    Friend WithEvents receivable_date_stopped As DataGridViewTextBoxColumn
    Friend WithEvents receivable_remarks As DataGridViewTextBoxColumn
    Friend WithEvents action_edit As DataGridViewButtonColumn
    Friend WithEvents action_delete As DataGridViewButtonColumn
    Friend WithEvents action_resume As DataGridViewButtonColumn
    Friend WithEvents action_manage_rule As DataGridViewButtonColumn
    Friend WithEvents pnlNavigator As Panel
    Friend WithEvents btnAdd As Button
    Friend WithEvents txtSearch As TextBox
End Class
