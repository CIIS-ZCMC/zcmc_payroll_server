<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class FrmPayrollType
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
        Me.panelContainer = New System.Windows.Forms.Panel()
        Me.panelContent = New System.Windows.Forms.Panel()
        Me.btn13MonthPay = New System.Windows.Forms.Button()
        Me.btnRegularPayroll = New System.Windows.Forms.Button()
        Me.btnSpecialPayroll = New System.Windows.Forms.Button()
        Me.btnJobOrderPayroll = New System.Windows.Forms.Button()
        Me.btnNightDifferential = New System.Windows.Forms.Button()
        Me.Panel1 = New System.Windows.Forms.Panel()
        Me.panelBottom = New System.Windows.Forms.Panel()
        Me.panelTop = New System.Windows.Forms.Panel()
        Me.lblTitle = New System.Windows.Forms.Label()
        Me.panelContainer.SuspendLayout()
        Me.panelContent.SuspendLayout()
        Me.panelTop.SuspendLayout()
        Me.SuspendLayout()
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
        Me.panelContainer.TabIndex = 3
        '
        'panelContent
        '
        Me.panelContent.Controls.Add(Me.btn13MonthPay)
        Me.panelContent.Controls.Add(Me.btnRegularPayroll)
        Me.panelContent.Controls.Add(Me.btnSpecialPayroll)
        Me.panelContent.Controls.Add(Me.btnJobOrderPayroll)
        Me.panelContent.Controls.Add(Me.btnNightDifferential)
        Me.panelContent.Controls.Add(Me.Panel1)
        Me.panelContent.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContent.Location = New System.Drawing.Point(0, 66)
        Me.panelContent.Name = "panelContent"
        Me.panelContent.Size = New System.Drawing.Size(1107, 455)
        Me.panelContent.TabIndex = 35
        '
        'btn13MonthPay
        '
        Me.btn13MonthPay.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btn13MonthPay.Font = New System.Drawing.Font("Segoe UI Semibold", 15.75!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btn13MonthPay.Image = Global.zcmc_payroll_client_v2.My.Resources.Resources._13_month_128
        Me.btn13MonthPay.Location = New System.Drawing.Point(853, 145)
        Me.btn13MonthPay.Name = "btn13MonthPay"
        Me.btn13MonthPay.Size = New System.Drawing.Size(192, 218)
        Me.btn13MonthPay.TabIndex = 40
        Me.btn13MonthPay.Text = "13 Month Pay"
        Me.btn13MonthPay.TextAlign = System.Drawing.ContentAlignment.BottomCenter
        Me.btn13MonthPay.UseVisualStyleBackColor = True
        '
        'btnRegularPayroll
        '
        Me.btnRegularPayroll.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnRegularPayroll.Font = New System.Drawing.Font("Segoe UI Semibold", 15.75!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnRegularPayroll.Image = Global.zcmc_payroll_client_v2.My.Resources.Resources.paycheck_128
        Me.btnRegularPayroll.Location = New System.Drawing.Point(61, 145)
        Me.btnRegularPayroll.Name = "btnRegularPayroll"
        Me.btnRegularPayroll.Size = New System.Drawing.Size(192, 218)
        Me.btnRegularPayroll.TabIndex = 39
        Me.btnRegularPayroll.Text = "Regular Payroll" & Global.Microsoft.VisualBasic.ChrW(13) & Global.Microsoft.VisualBasic.ChrW(10)
        Me.btnRegularPayroll.TextAlign = System.Drawing.ContentAlignment.BottomCenter
        Me.btnRegularPayroll.UseVisualStyleBackColor = True
        '
        'btnSpecialPayroll
        '
        Me.btnSpecialPayroll.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnSpecialPayroll.Font = New System.Drawing.Font("Segoe UI Semibold", 15.75!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnSpecialPayroll.Image = Global.zcmc_payroll_client_v2.My.Resources.Resources.special_payroll_128
        Me.btnSpecialPayroll.Location = New System.Drawing.Point(655, 145)
        Me.btnSpecialPayroll.Name = "btnSpecialPayroll"
        Me.btnSpecialPayroll.Size = New System.Drawing.Size(192, 218)
        Me.btnSpecialPayroll.TabIndex = 38
        Me.btnSpecialPayroll.Text = "Special Payroll"
        Me.btnSpecialPayroll.TextAlign = System.Drawing.ContentAlignment.BottomCenter
        Me.btnSpecialPayroll.UseVisualStyleBackColor = True
        '
        'btnJobOrderPayroll
        '
        Me.btnJobOrderPayroll.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnJobOrderPayroll.Font = New System.Drawing.Font("Segoe UI Semibold", 15.75!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnJobOrderPayroll.Image = Global.zcmc_payroll_client_v2.My.Resources.Resources.paycheck_128
        Me.btnJobOrderPayroll.Location = New System.Drawing.Point(259, 145)
        Me.btnJobOrderPayroll.Name = "btnJobOrderPayroll"
        Me.btnJobOrderPayroll.Size = New System.Drawing.Size(192, 218)
        Me.btnJobOrderPayroll.TabIndex = 37
        Me.btnJobOrderPayroll.Text = "Job Order Payroll"
        Me.btnJobOrderPayroll.TextAlign = System.Drawing.ContentAlignment.BottomCenter
        Me.btnJobOrderPayroll.UseVisualStyleBackColor = True
        '
        'btnNightDifferential
        '
        Me.btnNightDifferential.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnNightDifferential.Font = New System.Drawing.Font("Segoe UI Semibold", 15.75!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnNightDifferential.Image = Global.zcmc_payroll_client_v2.My.Resources.Resources.night_payroll_128
        Me.btnNightDifferential.Location = New System.Drawing.Point(457, 145)
        Me.btnNightDifferential.Name = "btnNightDifferential"
        Me.btnNightDifferential.Size = New System.Drawing.Size(192, 218)
        Me.btnNightDifferential.TabIndex = 36
        Me.btnNightDifferential.Text = "Night Differential"
        Me.btnNightDifferential.TextAlign = System.Drawing.ContentAlignment.BottomCenter
        Me.btnNightDifferential.UseVisualStyleBackColor = True
        '
        'Panel1
        '
        Me.Panel1.BackColor = System.Drawing.Color.WhiteSmoke
        Me.Panel1.Dock = System.Windows.Forms.DockStyle.Top
        Me.Panel1.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Pixel)
        Me.Panel1.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        Me.Panel1.Location = New System.Drawing.Point(0, 0)
        Me.Panel1.Name = "Panel1"
        Me.Panel1.Size = New System.Drawing.Size(1107, 68)
        Me.Panel1.TabIndex = 35
        '
        'panelBottom
        '
        Me.panelBottom.BackColor = System.Drawing.Color.WhiteSmoke
        Me.panelBottom.Dock = System.Windows.Forms.DockStyle.Bottom
        Me.panelBottom.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Pixel)
        Me.panelBottom.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        Me.panelBottom.Location = New System.Drawing.Point(0, 521)
        Me.panelBottom.Name = "panelBottom"
        Me.panelBottom.Size = New System.Drawing.Size(1107, 67)
        Me.panelBottom.TabIndex = 34
        '
        'panelTop
        '
        Me.panelTop.BackColor = System.Drawing.Color.FromArgb(CType(CType(10, Byte), Integer), CType(CType(62, Byte), Integer), CType(CType(48, Byte), Integer))
        Me.panelTop.Controls.Add(Me.lblTitle)
        Me.panelTop.Dock = System.Windows.Forms.DockStyle.Top
        Me.panelTop.Location = New System.Drawing.Point(0, 0)
        Me.panelTop.Name = "panelTop"
        Me.panelTop.Size = New System.Drawing.Size(1107, 66)
        Me.panelTop.TabIndex = 32
        '
        'lblTitle
        '
        Me.lblTitle.Anchor = System.Windows.Forms.AnchorStyles.Top
        Me.lblTitle.AutoSize = True
        Me.lblTitle.Font = New System.Drawing.Font("Segoe UI Semibold", 36.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblTitle.ForeColor = System.Drawing.Color.White
        Me.lblTitle.Location = New System.Drawing.Point(282, 1)
        Me.lblTitle.Name = "lblTitle"
        Me.lblTitle.Size = New System.Drawing.Size(543, 65)
        Me.lblTitle.TabIndex = 2
        Me.lblTitle.Text = "CHOOSE PAYROLL TYPE"
        '
        'FrmPayrollType
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.White
        Me.ClientSize = New System.Drawing.Size(1107, 588)
        Me.Controls.Add(Me.panelContainer)
        Me.FormBorderStyle = System.Windows.Forms.FormBorderStyle.None
        Me.Name = "FrmPayrollType"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterParent
        Me.Text = "FrmPayrollType"
        Me.panelContainer.ResumeLayout(False)
        Me.panelContent.ResumeLayout(False)
        Me.panelTop.ResumeLayout(False)
        Me.panelTop.PerformLayout()
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents panelContainer As Panel
    Friend WithEvents panelContent As Panel
    Friend WithEvents Panel1 As Panel
    Friend WithEvents panelBottom As Panel
    Friend WithEvents panelTop As Panel
    Friend WithEvents lblTitle As Label
    Friend WithEvents btnNightDifferential As Button
    Friend WithEvents btn13MonthPay As Button
    Friend WithEvents btnRegularPayroll As Button
    Friend WithEvents btnSpecialPayroll As Button
    Friend WithEvents btnJobOrderPayroll As Button
End Class
