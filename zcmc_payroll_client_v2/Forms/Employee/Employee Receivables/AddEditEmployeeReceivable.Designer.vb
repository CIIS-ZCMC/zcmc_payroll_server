<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class AddEditEmployeeReceivable
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
        Me.TableLayoutPanelContainer = New System.Windows.Forms.TableLayoutPanel()
        Me.panelTop = New System.Windows.Forms.Panel()
        Me.lblTitle = New System.Windows.Forms.Label()
        Me.panelContainer = New System.Windows.Forms.Panel()
        Me.GroupBox1 = New System.Windows.Forms.GroupBox()
        Me.Label6 = New System.Windows.Forms.Label()
        Me.txtReason = New System.Windows.Forms.TextBox()
        Me.GroupBox2 = New System.Windows.Forms.GroupBox()
        Me.txtPreferredAmount = New System.Windows.Forms.TextBox()
        Me.txtSalaryPercentage = New System.Windows.Forms.TextBox()
        Me.txtDefaultAmount = New System.Windows.Forms.TextBox()
        Me.Label5 = New System.Windows.Forms.Label()
        Me.Label4 = New System.Windows.Forms.Label()
        Me.lblDescription = New System.Windows.Forms.Label()
        Me.rdbPreferred = New System.Windows.Forms.RadioButton()
        Me.rdbPercentage = New System.Windows.Forms.RadioButton()
        Me.rdbDefault = New System.Windows.Forms.RadioButton()
        Me.cmbBillingCycle = New System.Windows.Forms.ComboBox()
        Me.Label2 = New System.Windows.Forms.Label()
        Me.cmbReceivable = New System.Windows.Forms.ComboBox()
        Me.Label1 = New System.Windows.Forms.Label()
        Me.panelBottom = New System.Windows.Forms.Panel()
        Me.btnCancel = New System.Windows.Forms.Button()
        Me.btnNext = New System.Windows.Forms.Button()
        Me.TableLayoutPanelContainer.SuspendLayout()
        Me.panelTop.SuspendLayout()
        Me.panelContainer.SuspendLayout()
        Me.GroupBox1.SuspendLayout()
        Me.GroupBox2.SuspendLayout()
        Me.panelBottom.SuspendLayout()
        Me.SuspendLayout()
        '
        'TableLayoutPanelContainer
        '
        Me.TableLayoutPanelContainer.CellBorderStyle = System.Windows.Forms.TableLayoutPanelCellBorderStyle.[Single]
        Me.TableLayoutPanelContainer.ColumnCount = 1
        Me.TableLayoutPanelContainer.ColumnStyles.Add(New System.Windows.Forms.ColumnStyle(System.Windows.Forms.SizeType.Percent, 50.0!))
        Me.TableLayoutPanelContainer.Controls.Add(Me.panelTop, 0, 0)
        Me.TableLayoutPanelContainer.Controls.Add(Me.panelContainer, 0, 1)
        Me.TableLayoutPanelContainer.Controls.Add(Me.panelBottom, 0, 2)
        Me.TableLayoutPanelContainer.Dock = System.Windows.Forms.DockStyle.Fill
        Me.TableLayoutPanelContainer.Location = New System.Drawing.Point(0, 0)
        Me.TableLayoutPanelContainer.Name = "TableLayoutPanelContainer"
        Me.TableLayoutPanelContainer.RowCount = 3
        Me.TableLayoutPanelContainer.RowStyles.Add(New System.Windows.Forms.RowStyle(System.Windows.Forms.SizeType.Percent, 10.2459!))
        Me.TableLayoutPanelContainer.RowStyles.Add(New System.Windows.Forms.RowStyle(System.Windows.Forms.SizeType.Percent, 89.7541!))
        Me.TableLayoutPanelContainer.RowStyles.Add(New System.Windows.Forms.RowStyle(System.Windows.Forms.SizeType.Absolute, 52.0!))
        Me.TableLayoutPanelContainer.Size = New System.Drawing.Size(519, 543)
        Me.TableLayoutPanelContainer.TabIndex = 8
        '
        'panelTop
        '
        Me.panelTop.BackColor = System.Drawing.Color.FromArgb(CType(CType(10, Byte), Integer), CType(CType(62, Byte), Integer), CType(CType(48, Byte), Integer))
        Me.panelTop.Controls.Add(Me.lblTitle)
        Me.panelTop.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelTop.Location = New System.Drawing.Point(4, 4)
        Me.panelTop.Name = "panelTop"
        Me.panelTop.Size = New System.Drawing.Size(511, 43)
        Me.panelTop.TabIndex = 0
        '
        'lblTitle
        '
        Me.lblTitle.AutoSize = True
        Me.lblTitle.Font = New System.Drawing.Font("Segoe UI", 18.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblTitle.ForeColor = System.Drawing.Color.White
        Me.lblTitle.Location = New System.Drawing.Point(166, 9)
        Me.lblTitle.Name = "lblTitle"
        Me.lblTitle.Size = New System.Drawing.Size(189, 32)
        Me.lblTitle.TabIndex = 2
        Me.lblTitle.Text = "Add Receivable"
        '
        'panelContainer
        '
        Me.panelContainer.Controls.Add(Me.GroupBox1)
        Me.panelContainer.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContainer.Location = New System.Drawing.Point(4, 54)
        Me.panelContainer.Name = "panelContainer"
        Me.panelContainer.Size = New System.Drawing.Size(511, 431)
        Me.panelContainer.TabIndex = 1
        '
        'GroupBox1
        '
        Me.GroupBox1.Anchor = CType((((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom) _
            Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.GroupBox1.Controls.Add(Me.Label6)
        Me.GroupBox1.Controls.Add(Me.txtReason)
        Me.GroupBox1.Controls.Add(Me.GroupBox2)
        Me.GroupBox1.Controls.Add(Me.cmbBillingCycle)
        Me.GroupBox1.Controls.Add(Me.Label2)
        Me.GroupBox1.Controls.Add(Me.cmbReceivable)
        Me.GroupBox1.Controls.Add(Me.Label1)
        Me.GroupBox1.Font = New System.Drawing.Font("Segoe UI Semibold", 9.75!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.GroupBox1.Location = New System.Drawing.Point(8, 3)
        Me.GroupBox1.Name = "GroupBox1"
        Me.GroupBox1.Size = New System.Drawing.Size(495, 425)
        Me.GroupBox1.TabIndex = 43
        Me.GroupBox1.TabStop = False
        Me.GroupBox1.Text = "General Information"
        '
        'Label6
        '
        Me.Label6.AutoSize = True
        Me.Label6.BackColor = System.Drawing.Color.White
        Me.Label6.Font = New System.Drawing.Font("Segoe UI Semibold", 11.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label6.ForeColor = System.Drawing.Color.Black
        Me.Label6.Location = New System.Drawing.Point(6, 316)
        Me.Label6.Name = "Label6"
        Me.Label6.Size = New System.Drawing.Size(138, 20)
        Me.Label6.TabIndex = 53
        Me.Label6.Text = "Reason for adding:"
        '
        'txtReason
        '
        Me.txtReason.Location = New System.Drawing.Point(10, 339)
        Me.txtReason.Multiline = True
        Me.txtReason.Name = "txtReason"
        Me.txtReason.Size = New System.Drawing.Size(466, 81)
        Me.txtReason.TabIndex = 52
        '
        'GroupBox2
        '
        Me.GroupBox2.Controls.Add(Me.txtPreferredAmount)
        Me.GroupBox2.Controls.Add(Me.txtSalaryPercentage)
        Me.GroupBox2.Controls.Add(Me.txtDefaultAmount)
        Me.GroupBox2.Controls.Add(Me.Label5)
        Me.GroupBox2.Controls.Add(Me.Label4)
        Me.GroupBox2.Controls.Add(Me.lblDescription)
        Me.GroupBox2.Controls.Add(Me.rdbPreferred)
        Me.GroupBox2.Controls.Add(Me.rdbPercentage)
        Me.GroupBox2.Controls.Add(Me.rdbDefault)
        Me.GroupBox2.Location = New System.Drawing.Point(6, 96)
        Me.GroupBox2.Name = "GroupBox2"
        Me.GroupBox2.Size = New System.Drawing.Size(484, 217)
        Me.GroupBox2.TabIndex = 51
        Me.GroupBox2.TabStop = False
        Me.GroupBox2.Text = "Select Amount"
        '
        'txtPreferredAmount
        '
        Me.txtPreferredAmount.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle
        Me.txtPreferredAmount.Enabled = False
        Me.txtPreferredAmount.Location = New System.Drawing.Point(266, 157)
        Me.txtPreferredAmount.Name = "txtPreferredAmount"
        Me.txtPreferredAmount.Size = New System.Drawing.Size(204, 25)
        Me.txtPreferredAmount.TabIndex = 66
        '
        'txtSalaryPercentage
        '
        Me.txtSalaryPercentage.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle
        Me.txtSalaryPercentage.Enabled = False
        Me.txtSalaryPercentage.Location = New System.Drawing.Point(266, 90)
        Me.txtSalaryPercentage.Name = "txtSalaryPercentage"
        Me.txtSalaryPercentage.Size = New System.Drawing.Size(204, 25)
        Me.txtSalaryPercentage.TabIndex = 65
        '
        'txtDefaultAmount
        '
        Me.txtDefaultAmount.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle
        Me.txtDefaultAmount.Enabled = False
        Me.txtDefaultAmount.Location = New System.Drawing.Point(266, 28)
        Me.txtDefaultAmount.Name = "txtDefaultAmount"
        Me.txtDefaultAmount.Size = New System.Drawing.Size(204, 25)
        Me.txtDefaultAmount.TabIndex = 64
        '
        'Label5
        '
        Me.Label5.AutoSize = True
        Me.Label5.Font = New System.Drawing.Font("Segoe UI", 10.0!)
        Me.Label5.ForeColor = System.Drawing.Color.DimGray
        Me.Label5.Location = New System.Drawing.Point(29, 184)
        Me.Label5.Name = "Label5"
        Me.Label5.Size = New System.Drawing.Size(358, 19)
        Me.Label5.TabIndex = 61
        Me.Label5.Text = "This change will be only applied to the selected employee"
        '
        'Label4
        '
        Me.Label4.AutoSize = True
        Me.Label4.Font = New System.Drawing.Font("Segoe UI", 10.0!)
        Me.Label4.ForeColor = System.Drawing.Color.DimGray
        Me.Label4.Location = New System.Drawing.Point(29, 117)
        Me.Label4.Name = "Label4"
        Me.Label4.Size = New System.Drawing.Size(358, 19)
        Me.Label4.TabIndex = 60
        Me.Label4.Text = "This change will be only applied to the selected employee"
        '
        'lblDescription
        '
        Me.lblDescription.AutoSize = True
        Me.lblDescription.Font = New System.Drawing.Font("Segoe UI", 10.0!)
        Me.lblDescription.ForeColor = System.Drawing.Color.DimGray
        Me.lblDescription.Location = New System.Drawing.Point(29, 58)
        Me.lblDescription.Name = "lblDescription"
        Me.lblDescription.Size = New System.Drawing.Size(358, 19)
        Me.lblDescription.TabIndex = 59
        Me.lblDescription.Text = "This change will be only applied to the selected employee"
        '
        'rdbPreferred
        '
        Me.rdbPreferred.AutoSize = True
        Me.rdbPreferred.Font = New System.Drawing.Font("Segoe UI Semibold", 11.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.rdbPreferred.Location = New System.Drawing.Point(33, 157)
        Me.rdbPreferred.Name = "rdbPreferred"
        Me.rdbPreferred.Size = New System.Drawing.Size(151, 24)
        Me.rdbPreferred.TabIndex = 58
        Me.rdbPreferred.TabStop = True
        Me.rdbPreferred.Text = "Preferred Amount"
        Me.rdbPreferred.UseVisualStyleBackColor = True
        '
        'rdbPercentage
        '
        Me.rdbPercentage.AutoSize = True
        Me.rdbPercentage.Font = New System.Drawing.Font("Segoe UI Semibold", 11.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.rdbPercentage.Location = New System.Drawing.Point(33, 90)
        Me.rdbPercentage.Name = "rdbPercentage"
        Me.rdbPercentage.Size = New System.Drawing.Size(166, 24)
        Me.rdbPercentage.TabIndex = 56
        Me.rdbPercentage.TabStop = True
        Me.rdbPercentage.Text = "Percentage of salary"
        Me.rdbPercentage.UseVisualStyleBackColor = True
        '
        'rdbDefault
        '
        Me.rdbDefault.AutoSize = True
        Me.rdbDefault.Font = New System.Drawing.Font("Segoe UI Semibold", 11.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.rdbDefault.Location = New System.Drawing.Point(33, 28)
        Me.rdbDefault.Name = "rdbDefault"
        Me.rdbDefault.Size = New System.Drawing.Size(136, 24)
        Me.rdbDefault.TabIndex = 54
        Me.rdbDefault.TabStop = True
        Me.rdbDefault.Text = "Default Amount"
        Me.rdbDefault.UseVisualStyleBackColor = True
        '
        'cmbBillingCycle
        '
        Me.cmbBillingCycle.Font = New System.Drawing.Font("Segoe UI", 12.0!)
        Me.cmbBillingCycle.FormattingEnabled = True
        Me.cmbBillingCycle.Items.AddRange(New Object() {"Annual", "Monthly", "Quarterly"})
        Me.cmbBillingCycle.Location = New System.Drawing.Point(272, 61)
        Me.cmbBillingCycle.Name = "cmbBillingCycle"
        Me.cmbBillingCycle.Size = New System.Drawing.Size(204, 29)
        Me.cmbBillingCycle.TabIndex = 46
        '
        'Label2
        '
        Me.Label2.AutoSize = True
        Me.Label2.BackColor = System.Drawing.Color.White
        Me.Label2.Font = New System.Drawing.Font("Segoe UI Semibold", 11.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label2.ForeColor = System.Drawing.Color.Black
        Me.Label2.Location = New System.Drawing.Point(6, 65)
        Me.Label2.Name = "Label2"
        Me.Label2.Size = New System.Drawing.Size(92, 20)
        Me.Label2.TabIndex = 45
        Me.Label2.Text = "Billing Cycle"
        '
        'cmbReceivable
        '
        Me.cmbReceivable.Font = New System.Drawing.Font("Segoe UI", 12.0!)
        Me.cmbReceivable.FormattingEnabled = True
        Me.cmbReceivable.Location = New System.Drawing.Point(272, 26)
        Me.cmbReceivable.Name = "cmbReceivable"
        Me.cmbReceivable.Size = New System.Drawing.Size(204, 29)
        Me.cmbReceivable.TabIndex = 44
        '
        'Label1
        '
        Me.Label1.AutoSize = True
        Me.Label1.BackColor = System.Drawing.Color.White
        Me.Label1.Font = New System.Drawing.Font("Segoe UI Semibold", 11.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label1.ForeColor = System.Drawing.Color.Black
        Me.Label1.Location = New System.Drawing.Point(6, 30)
        Me.Label1.Name = "Label1"
        Me.Label1.Size = New System.Drawing.Size(145, 20)
        Me.Label1.TabIndex = 43
        Me.Label1.Text = "Name of Receivable"
        '
        'panelBottom
        '
        Me.panelBottom.BackColor = System.Drawing.Color.WhiteSmoke
        Me.panelBottom.Controls.Add(Me.btnCancel)
        Me.panelBottom.Controls.Add(Me.btnNext)
        Me.panelBottom.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelBottom.Location = New System.Drawing.Point(4, 492)
        Me.panelBottom.Name = "panelBottom"
        Me.panelBottom.Size = New System.Drawing.Size(511, 47)
        Me.panelBottom.TabIndex = 2
        '
        'btnCancel
        '
        Me.btnCancel.BackColor = System.Drawing.Color.White
        Me.btnCancel.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnCancel.Font = New System.Drawing.Font("Segoe UI", 12.0!)
        Me.btnCancel.ForeColor = System.Drawing.Color.Black
        Me.btnCancel.Location = New System.Drawing.Point(100, 6)
        Me.btnCancel.Name = "btnCancel"
        Me.btnCancel.Size = New System.Drawing.Size(132, 33)
        Me.btnCancel.TabIndex = 5
        Me.btnCancel.Text = "Cancel"
        Me.btnCancel.UseVisualStyleBackColor = False
        '
        'btnNext
        '
        Me.btnNext.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnNext.BackColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.btnNext.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnNext.Font = New System.Drawing.Font("Segoe UI", 12.0!)
        Me.btnNext.ForeColor = System.Drawing.Color.White
        Me.btnNext.Location = New System.Drawing.Point(238, 6)
        Me.btnNext.Name = "btnNext"
        Me.btnNext.Size = New System.Drawing.Size(132, 33)
        Me.btnNext.TabIndex = 4
        Me.btnNext.Text = "Next"
        Me.btnNext.UseVisualStyleBackColor = False
        '
        'AddEditEmployeeReceivable
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.White
        Me.ClientSize = New System.Drawing.Size(519, 543)
        Me.Controls.Add(Me.TableLayoutPanelContainer)
        Me.FormBorderStyle = System.Windows.Forms.FormBorderStyle.None
        Me.Name = "AddEditEmployeeReceivable"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen
        Me.Text = "AddEditEmployeeReceivable"
        Me.TableLayoutPanelContainer.ResumeLayout(False)
        Me.panelTop.ResumeLayout(False)
        Me.panelTop.PerformLayout()
        Me.panelContainer.ResumeLayout(False)
        Me.GroupBox1.ResumeLayout(False)
        Me.GroupBox1.PerformLayout()
        Me.GroupBox2.ResumeLayout(False)
        Me.GroupBox2.PerformLayout()
        Me.panelBottom.ResumeLayout(False)
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents TableLayoutPanelContainer As TableLayoutPanel
    Friend WithEvents panelTop As Panel
    Friend WithEvents panelContainer As Panel
    Friend WithEvents GroupBox1 As GroupBox
    Friend WithEvents Label6 As Label
    Friend WithEvents txtReason As TextBox
    Friend WithEvents GroupBox2 As GroupBox
    Friend WithEvents txtPreferredAmount As TextBox
    Friend WithEvents txtSalaryPercentage As TextBox
    Friend WithEvents txtDefaultAmount As TextBox
    Friend WithEvents Label5 As Label
    Friend WithEvents Label4 As Label
    Friend WithEvents lblDescription As Label
    Friend WithEvents rdbPreferred As RadioButton
    Friend WithEvents rdbPercentage As RadioButton
    Friend WithEvents rdbDefault As RadioButton
    Friend WithEvents cmbBillingCycle As ComboBox
    Friend WithEvents Label2 As Label
    Friend WithEvents cmbReceivable As ComboBox
    Friend WithEvents Label1 As Label
    Friend WithEvents panelBottom As Panel
    Friend WithEvents btnCancel As Button
    Friend WithEvents btnNext As Button
    Friend WithEvents lblTitle As Label
End Class
