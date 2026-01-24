<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()>
Partial Class UcPayrollStepOne
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
        Me.panelContainer = New System.Windows.Forms.Panel()
        Me.panelContent = New System.Windows.Forms.Panel()
        Me.GroupBox5 = New System.Windows.Forms.GroupBox()
        Me.PictureBoxReminder = New System.Windows.Forms.PictureBox()
        Me.lblReminder = New System.Windows.Forms.Label()
        Me.rdbGeneralPayroll = New System.Windows.Forms.RadioButton()
        Me.rdbSpecialPayroll = New System.Windows.Forms.RadioButton()
        Me.GroupBox1 = New System.Windows.Forms.GroupBox()
        Me.GroupBox4 = New System.Windows.Forms.GroupBox()
        Me.rdb16to31 = New System.Windows.Forms.RadioButton()
        Me.rdb1to15 = New System.Windows.Forms.RadioButton()
        Me.GroupBox3 = New System.Windows.Forms.GroupBox()
        Me.rdb24Days = New System.Windows.Forms.RadioButton()
        Me.rdb22Days = New System.Windows.Forms.RadioButton()
        Me.GroupBox2 = New System.Windows.Forms.GroupBox()
        Me.rdbJobOrder = New System.Windows.Forms.RadioButton()
        Me.rdbRegular = New System.Windows.Forms.RadioButton()
        Me.lblPayrollMonth = New System.Windows.Forms.Label()
        Me.Label1 = New System.Windows.Forms.Label()
        Me.panelTop = New System.Windows.Forms.Panel()
        Me.lblDescription = New System.Windows.Forms.Label()
        Me.lblTitle = New System.Windows.Forms.Label()
        Me.panelContainer.SuspendLayout()
        Me.panelContent.SuspendLayout()
        Me.GroupBox5.SuspendLayout()
        CType(Me.PictureBoxReminder, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.GroupBox1.SuspendLayout()
        Me.GroupBox4.SuspendLayout()
        Me.GroupBox3.SuspendLayout()
        Me.GroupBox2.SuspendLayout()
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
        Me.panelContainer.Size = New System.Drawing.Size(615, 642)
        Me.panelContainer.TabIndex = 0
        '
        'panelContent
        '
        Me.panelContent.Controls.Add(Me.GroupBox5)
        Me.panelContent.Controls.Add(Me.GroupBox1)
        Me.panelContent.Controls.Add(Me.lblPayrollMonth)
        Me.panelContent.Controls.Add(Me.Label1)
        Me.panelContent.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContent.Location = New System.Drawing.Point(0, 66)
        Me.panelContent.Name = "panelContent"
        Me.panelContent.Size = New System.Drawing.Size(615, 576)
        Me.panelContent.TabIndex = 35
        '
        'GroupBox5
        '
        Me.GroupBox5.Anchor = System.Windows.Forms.AnchorStyles.Top
        Me.GroupBox5.Controls.Add(Me.PictureBoxReminder)
        Me.GroupBox5.Controls.Add(Me.lblReminder)
        Me.GroupBox5.Controls.Add(Me.rdbGeneralPayroll)
        Me.GroupBox5.Controls.Add(Me.rdbSpecialPayroll)
        Me.GroupBox5.Font = New System.Drawing.Font("Segoe UI Semibold", 10.0!, System.Drawing.FontStyle.Bold)
        Me.GroupBox5.Location = New System.Drawing.Point(92, 275)
        Me.GroupBox5.Name = "GroupBox5"
        Me.GroupBox5.Size = New System.Drawing.Size(431, 134)
        Me.GroupBox5.TabIndex = 41
        Me.GroupBox5.TabStop = False
        Me.GroupBox5.Text = "Payroll type"
        '
        'PictureBoxReminder
        '
        Me.PictureBoxReminder.Anchor = System.Windows.Forms.AnchorStyles.Top
        Me.PictureBoxReminder.BackgroundImage = Global.zcmc_payroll_client_v2.My.Resources.Resources.completed_payroll_32
        Me.PictureBoxReminder.BackgroundImageLayout = System.Windows.Forms.ImageLayout.Zoom
        Me.PictureBoxReminder.Location = New System.Drawing.Point(65, 56)
        Me.PictureBoxReminder.Name = "PictureBoxReminder"
        Me.PictureBoxReminder.Size = New System.Drawing.Size(51, 46)
        Me.PictureBoxReminder.TabIndex = 1
        Me.PictureBoxReminder.TabStop = False
        '
        'lblReminder
        '
        Me.lblReminder.Anchor = System.Windows.Forms.AnchorStyles.Top
        Me.lblReminder.AutoSize = True
        Me.lblReminder.Font = New System.Drawing.Font("Segoe UI Semibold", 12.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblReminder.ForeColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.lblReminder.Location = New System.Drawing.Point(124, 60)
        Me.lblReminder.Name = "lblReminder"
        Me.lblReminder.Size = New System.Drawing.Size(242, 42)
        Me.lblReminder.TabIndex = 0
        Me.lblReminder.Text = "Payroll for the set active month " & Global.Microsoft.VisualBasic.ChrW(13) & Global.Microsoft.VisualBasic.ChrW(10) & "has already been generated."
        '
        'rdbGeneralPayroll
        '
        Me.rdbGeneralPayroll.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom), System.Windows.Forms.AnchorStyles)
        Me.rdbGeneralPayroll.AutoSize = True
        Me.rdbGeneralPayroll.FlatStyle = System.Windows.Forms.FlatStyle.System
        Me.rdbGeneralPayroll.Font = New System.Drawing.Font("Segoe UI Semibold", 12.0!, System.Drawing.FontStyle.Bold)
        Me.rdbGeneralPayroll.Location = New System.Drawing.Point(65, 24)
        Me.rdbGeneralPayroll.Name = "rdbGeneralPayroll"
        Me.rdbGeneralPayroll.Size = New System.Drawing.Size(143, 26)
        Me.rdbGeneralPayroll.TabIndex = 28
        Me.rdbGeneralPayroll.TabStop = True
        Me.rdbGeneralPayroll.Text = "General Payroll"
        Me.rdbGeneralPayroll.UseVisualStyleBackColor = True
        '
        'rdbSpecialPayroll
        '
        Me.rdbSpecialPayroll.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom), System.Windows.Forms.AnchorStyles)
        Me.rdbSpecialPayroll.AutoSize = True
        Me.rdbSpecialPayroll.FlatStyle = System.Windows.Forms.FlatStyle.System
        Me.rdbSpecialPayroll.Font = New System.Drawing.Font("Segoe UI Semibold", 12.0!, System.Drawing.FontStyle.Bold)
        Me.rdbSpecialPayroll.Location = New System.Drawing.Point(218, 24)
        Me.rdbSpecialPayroll.Name = "rdbSpecialPayroll"
        Me.rdbSpecialPayroll.Size = New System.Drawing.Size(139, 26)
        Me.rdbSpecialPayroll.TabIndex = 27
        Me.rdbSpecialPayroll.TabStop = True
        Me.rdbSpecialPayroll.Text = "Special Payroll"
        Me.rdbSpecialPayroll.UseVisualStyleBackColor = True
        '
        'GroupBox1
        '
        Me.GroupBox1.Anchor = System.Windows.Forms.AnchorStyles.Top
        Me.GroupBox1.Controls.Add(Me.GroupBox4)
        Me.GroupBox1.Controls.Add(Me.GroupBox3)
        Me.GroupBox1.Controls.Add(Me.GroupBox2)
        Me.GroupBox1.Font = New System.Drawing.Font("Segoe UI Semibold", 10.0!, System.Drawing.FontStyle.Bold)
        Me.GroupBox1.Location = New System.Drawing.Point(92, 125)
        Me.GroupBox1.Name = "GroupBox1"
        Me.GroupBox1.Size = New System.Drawing.Size(430, 144)
        Me.GroupBox1.TabIndex = 40
        Me.GroupBox1.TabStop = False
        '
        'GroupBox4
        '
        Me.GroupBox4.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom), System.Windows.Forms.AnchorStyles)
        Me.GroupBox4.BackColor = System.Drawing.Color.White
        Me.GroupBox4.Controls.Add(Me.rdb16to31)
        Me.GroupBox4.Controls.Add(Me.rdb1to15)
        Me.GroupBox4.Enabled = False
        Me.GroupBox4.Font = New System.Drawing.Font("Segoe UI Semibold", 10.0!, System.Drawing.FontStyle.Bold)
        Me.GroupBox4.Location = New System.Drawing.Point(168, 78)
        Me.GroupBox4.Name = "GroupBox4"
        Me.GroupBox4.Size = New System.Drawing.Size(256, 54)
        Me.GroupBox4.TabIndex = 9
        Me.GroupBox4.TabStop = False
        Me.GroupBox4.Text = "Salary period"
        '
        'rdb16to31
        '
        Me.rdb16to31.AutoSize = True
        Me.rdb16to31.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.rdb16to31.Font = New System.Drawing.Font("Segoe UI Semibold", 12.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.rdb16to31.Location = New System.Drawing.Point(120, 22)
        Me.rdb16to31.Name = "rdb16to31"
        Me.rdb16to31.Size = New System.Drawing.Size(71, 25)
        Me.rdb16to31.TabIndex = 6
        Me.rdb16to31.Text = "16 - 31"
        Me.rdb16to31.UseVisualStyleBackColor = True
        '
        'rdb1to15
        '
        Me.rdb1to15.AutoSize = True
        Me.rdb1to15.Checked = True
        Me.rdb1to15.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.rdb1to15.Font = New System.Drawing.Font("Segoe UI Semibold", 12.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.rdb1to15.ForeColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.rdb1to15.Location = New System.Drawing.Point(25, 22)
        Me.rdb1to15.Name = "rdb1to15"
        Me.rdb1to15.Size = New System.Drawing.Size(62, 25)
        Me.rdb1to15.TabIndex = 5
        Me.rdb1to15.TabStop = True
        Me.rdb1to15.Text = "1 - 15"
        Me.rdb1to15.UseVisualStyleBackColor = True
        '
        'GroupBox3
        '
        Me.GroupBox3.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom), System.Windows.Forms.AnchorStyles)
        Me.GroupBox3.BackColor = System.Drawing.Color.White
        Me.GroupBox3.Controls.Add(Me.rdb24Days)
        Me.GroupBox3.Controls.Add(Me.rdb22Days)
        Me.GroupBox3.Enabled = False
        Me.GroupBox3.Font = New System.Drawing.Font("Segoe UI Semibold", 10.0!, System.Drawing.FontStyle.Bold)
        Me.GroupBox3.Location = New System.Drawing.Point(168, 18)
        Me.GroupBox3.Name = "GroupBox3"
        Me.GroupBox3.Size = New System.Drawing.Size(256, 54)
        Me.GroupBox3.TabIndex = 8
        Me.GroupBox3.TabStop = False
        Me.GroupBox3.Text = "Select days of duty"
        '
        'rdb24Days
        '
        Me.rdb24Days.AutoSize = True
        Me.rdb24Days.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.rdb24Days.Font = New System.Drawing.Font("Segoe UI Semibold", 12.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.rdb24Days.Location = New System.Drawing.Point(120, 22)
        Me.rdb24Days.Name = "rdb24Days"
        Me.rdb24Days.Size = New System.Drawing.Size(82, 25)
        Me.rdb24Days.TabIndex = 6
        Me.rdb24Days.Text = "24 days"
        Me.rdb24Days.UseVisualStyleBackColor = True
        '
        'rdb22Days
        '
        Me.rdb22Days.AutoSize = True
        Me.rdb22Days.Checked = True
        Me.rdb22Days.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.rdb22Days.Font = New System.Drawing.Font("Segoe UI Semibold", 12.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.rdb22Days.Location = New System.Drawing.Point(25, 22)
        Me.rdb22Days.Name = "rdb22Days"
        Me.rdb22Days.Size = New System.Drawing.Size(82, 25)
        Me.rdb22Days.TabIndex = 5
        Me.rdb22Days.TabStop = True
        Me.rdb22Days.Text = "22 days"
        Me.rdb22Days.UseVisualStyleBackColor = True
        '
        'GroupBox2
        '
        Me.GroupBox2.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom), System.Windows.Forms.AnchorStyles)
        Me.GroupBox2.Controls.Add(Me.rdbJobOrder)
        Me.GroupBox2.Controls.Add(Me.rdbRegular)
        Me.GroupBox2.Font = New System.Drawing.Font("Segoe UI Semibold", 10.0!, System.Drawing.FontStyle.Bold)
        Me.GroupBox2.Location = New System.Drawing.Point(6, 18)
        Me.GroupBox2.Name = "GroupBox2"
        Me.GroupBox2.Size = New System.Drawing.Size(156, 114)
        Me.GroupBox2.TabIndex = 0
        Me.GroupBox2.TabStop = False
        Me.GroupBox2.Text = "Employment Type"
        '
        'rdbJobOrder
        '
        Me.rdbJobOrder.AutoSize = True
        Me.rdbJobOrder.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.rdbJobOrder.Font = New System.Drawing.Font("Segoe UI Semibold", 12.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.rdbJobOrder.Location = New System.Drawing.Point(23, 60)
        Me.rdbJobOrder.Name = "rdbJobOrder"
        Me.rdbJobOrder.Size = New System.Drawing.Size(100, 25)
        Me.rdbJobOrder.TabIndex = 8
        Me.rdbJobOrder.TabStop = True
        Me.rdbJobOrder.Text = "Job Order"
        Me.rdbJobOrder.UseVisualStyleBackColor = True
        '
        'rdbRegular
        '
        Me.rdbRegular.AutoSize = True
        Me.rdbRegular.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.rdbRegular.Font = New System.Drawing.Font("Segoe UI Semibold", 12.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.rdbRegular.Location = New System.Drawing.Point(23, 28)
        Me.rdbRegular.Name = "rdbRegular"
        Me.rdbRegular.Size = New System.Drawing.Size(83, 25)
        Me.rdbRegular.TabIndex = 7
        Me.rdbRegular.TabStop = True
        Me.rdbRegular.Text = "Regular"
        Me.rdbRegular.UseVisualStyleBackColor = True
        '
        'lblPayrollMonth
        '
        Me.lblPayrollMonth.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom), System.Windows.Forms.AnchorStyles)
        Me.lblPayrollMonth.AutoSize = True
        Me.lblPayrollMonth.Font = New System.Drawing.Font("Segoe UI", 21.75!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblPayrollMonth.ForeColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.lblPayrollMonth.Location = New System.Drawing.Point(370, 88)
        Me.lblPayrollMonth.Name = "lblPayrollMonth"
        Me.lblPayrollMonth.Size = New System.Drawing.Size(153, 40)
        Me.lblPayrollMonth.TabIndex = 39
        Me.lblPayrollMonth.Text = "----- - ----"
        '
        'Label1
        '
        Me.Label1.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom), System.Windows.Forms.AnchorStyles)
        Me.Label1.AutoSize = True
        Me.Label1.Font = New System.Drawing.Font("Segoe UI", 18.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label1.Location = New System.Drawing.Point(86, 94)
        Me.Label1.Name = "Label1"
        Me.Label1.Size = New System.Drawing.Size(275, 32)
        Me.Label1.TabIndex = 38
        Me.Label1.Text = "Payroll for the month of:"
        '
        'panelTop
        '
        Me.panelTop.BackColor = System.Drawing.Color.FromArgb(CType(CType(10, Byte), Integer), CType(CType(62, Byte), Integer), CType(CType(48, Byte), Integer))
        Me.panelTop.Controls.Add(Me.lblDescription)
        Me.panelTop.Controls.Add(Me.lblTitle)
        Me.panelTop.Dock = System.Windows.Forms.DockStyle.Top
        Me.panelTop.Location = New System.Drawing.Point(0, 0)
        Me.panelTop.Name = "panelTop"
        Me.panelTop.Size = New System.Drawing.Size(615, 66)
        Me.panelTop.TabIndex = 34
        '
        'lblDescription
        '
        Me.lblDescription.Anchor = System.Windows.Forms.AnchorStyles.Top
        Me.lblDescription.AutoSize = True
        Me.lblDescription.Font = New System.Drawing.Font("Segoe UI", 10.0!)
        Me.lblDescription.ForeColor = System.Drawing.Color.White
        Me.lblDescription.Location = New System.Drawing.Point(128, 31)
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
        Me.lblTitle.Location = New System.Drawing.Point(184, 6)
        Me.lblTitle.Name = "lblTitle"
        Me.lblTitle.Size = New System.Drawing.Size(247, 21)
        Me.lblTitle.TabIndex = 2
        Me.lblTitle.Text = "Create Payroll - Step 1 of 4 : Set"
        '
        'UcPayrollStepOne
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.White
        Me.Controls.Add(Me.panelContainer)
        Me.Name = "UcPayrollStepOne"
        Me.Size = New System.Drawing.Size(615, 642)
        Me.panelContainer.ResumeLayout(False)
        Me.panelContent.ResumeLayout(False)
        Me.panelContent.PerformLayout()
        Me.GroupBox5.ResumeLayout(False)
        Me.GroupBox5.PerformLayout()
        CType(Me.PictureBoxReminder, System.ComponentModel.ISupportInitialize).EndInit()
        Me.GroupBox1.ResumeLayout(False)
        Me.GroupBox4.ResumeLayout(False)
        Me.GroupBox4.PerformLayout()
        Me.GroupBox3.ResumeLayout(False)
        Me.GroupBox3.PerformLayout()
        Me.GroupBox2.ResumeLayout(False)
        Me.GroupBox2.PerformLayout()
        Me.panelTop.ResumeLayout(False)
        Me.panelTop.PerformLayout()
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents panelContainer As Panel
    Friend WithEvents panelTop As Panel
    Friend WithEvents lblDescription As Label
    Friend WithEvents lblTitle As Label
    Friend WithEvents panelContent As Panel
    Friend WithEvents GroupBox5 As GroupBox
    Friend WithEvents PictureBoxReminder As PictureBox
    Friend WithEvents lblReminder As Label
    Friend WithEvents rdbGeneralPayroll As RadioButton
    Friend WithEvents rdbSpecialPayroll As RadioButton
    Friend WithEvents GroupBox1 As GroupBox
    Friend WithEvents GroupBox4 As GroupBox
    Friend WithEvents rdb16to31 As RadioButton
    Friend WithEvents rdb1to15 As RadioButton
    Friend WithEvents GroupBox3 As GroupBox
    Friend WithEvents rdb24Days As RadioButton
    Friend WithEvents rdb22Days As RadioButton
    Friend WithEvents GroupBox2 As GroupBox
    Friend WithEvents rdbJobOrder As RadioButton
    Friend WithEvents rdbRegular As RadioButton
    Friend WithEvents lblPayrollMonth As Label
    Friend WithEvents Label1 As Label
End Class
