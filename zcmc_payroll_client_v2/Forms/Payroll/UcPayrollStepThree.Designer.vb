<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class UcPayrollStepThree
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
        Me.panelTop = New System.Windows.Forms.Panel()
        Me.lblDescription = New System.Windows.Forms.Label()
        Me.lblTitle = New System.Windows.Forms.Label()
        Me.panelContainer = New System.Windows.Forms.Panel()
        Me.panelContent = New System.Windows.Forms.Panel()
        Me.progressbarValidating = New MetroFramework.Controls.MetroProgressBar()
        Me.lblSuccess = New System.Windows.Forms.Label()
        Me.pictureSuccess = New System.Windows.Forms.PictureBox()
        Me.panelTop.SuspendLayout()
        Me.panelContainer.SuspendLayout()
        Me.panelContent.SuspendLayout()
        CType(Me.pictureSuccess, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.SuspendLayout()
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
        Me.panelTop.TabIndex = 32
        '
        'lblDescription
        '
        Me.lblDescription.Anchor = System.Windows.Forms.AnchorStyles.Top
        Me.lblDescription.AutoSize = True
        Me.lblDescription.Font = New System.Drawing.Font("Segoe UI", 10.0!)
        Me.lblDescription.ForeColor = System.Drawing.Color.White
        Me.lblDescription.Location = New System.Drawing.Point(160, 31)
        Me.lblDescription.Name = "lblDescription"
        Me.lblDescription.Size = New System.Drawing.Size(294, 19)
        Me.lblDescription.TabIndex = 3
        Me.lblDescription.Text = "Validating and computing of employee records"
        '
        'lblTitle
        '
        Me.lblTitle.Anchor = System.Windows.Forms.AnchorStyles.Top
        Me.lblTitle.AutoSize = True
        Me.lblTitle.Font = New System.Drawing.Font("Segoe UI", 12.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblTitle.ForeColor = System.Drawing.Color.White
        Me.lblTitle.Location = New System.Drawing.Point(161, 6)
        Me.lblTitle.Name = "lblTitle"
        Me.lblTitle.Size = New System.Drawing.Size(292, 21)
        Me.lblTitle.TabIndex = 2
        Me.lblTitle.Text = "Create Payroll - Step 3 of 4 : Generate"
        '
        'panelContainer
        '
        Me.panelContainer.Controls.Add(Me.panelContent)
        Me.panelContainer.Controls.Add(Me.panelTop)
        Me.panelContainer.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContainer.Location = New System.Drawing.Point(0, 0)
        Me.panelContainer.Name = "panelContainer"
        Me.panelContainer.Size = New System.Drawing.Size(615, 642)
        Me.panelContainer.TabIndex = 2
        '
        'panelContent
        '
        Me.panelContent.Controls.Add(Me.progressbarValidating)
        Me.panelContent.Controls.Add(Me.lblSuccess)
        Me.panelContent.Controls.Add(Me.pictureSuccess)
        Me.panelContent.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContent.Location = New System.Drawing.Point(0, 66)
        Me.panelContent.Name = "panelContent"
        Me.panelContent.Size = New System.Drawing.Size(615, 576)
        Me.panelContent.TabIndex = 33
        '
        'progressbarValidating
        '
        Me.progressbarValidating.Anchor = System.Windows.Forms.AnchorStyles.Top
        Me.progressbarValidating.Location = New System.Drawing.Point(67, 274)
        Me.progressbarValidating.Name = "progressbarValidating"
        Me.progressbarValidating.Size = New System.Drawing.Size(480, 23)
        Me.progressbarValidating.TabIndex = 24
        '
        'lblSuccess
        '
        Me.lblSuccess.Anchor = System.Windows.Forms.AnchorStyles.Top
        Me.lblSuccess.AutoSize = True
        Me.lblSuccess.Font = New System.Drawing.Font("Segoe UI Semibold", 21.75!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblSuccess.ForeColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.lblSuccess.Location = New System.Drawing.Point(129, 220)
        Me.lblSuccess.Name = "lblSuccess"
        Me.lblSuccess.Size = New System.Drawing.Size(418, 40)
        Me.lblSuccess.TabIndex = 23
        Me.lblSuccess.Text = "Payroll Generated Successfully" & Global.Microsoft.VisualBasic.ChrW(13) & Global.Microsoft.VisualBasic.ChrW(10)
        '
        'pictureSuccess
        '
        Me.pictureSuccess.Anchor = System.Windows.Forms.AnchorStyles.Top
        Me.pictureSuccess.BackColor = System.Drawing.Color.White
        Me.pictureSuccess.BackgroundImage = Global.zcmc_payroll_client_v2.My.Resources.Resources.approved_32
        Me.pictureSuccess.BackgroundImageLayout = System.Windows.Forms.ImageLayout.Stretch
        Me.pictureSuccess.Location = New System.Drawing.Point(67, 213)
        Me.pictureSuccess.Name = "pictureSuccess"
        Me.pictureSuccess.Size = New System.Drawing.Size(56, 55)
        Me.pictureSuccess.TabIndex = 22
        Me.pictureSuccess.TabStop = False
        '
        'UcPayrollStepThree
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.White
        Me.Controls.Add(Me.panelContainer)
        Me.Name = "UcPayrollStepThree"
        Me.Size = New System.Drawing.Size(615, 642)
        Me.panelTop.ResumeLayout(False)
        Me.panelTop.PerformLayout()
        Me.panelContainer.ResumeLayout(False)
        Me.panelContent.ResumeLayout(False)
        Me.panelContent.PerformLayout()
        CType(Me.pictureSuccess, System.ComponentModel.ISupportInitialize).EndInit()
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents panelTop As Panel
    Friend WithEvents lblDescription As Label
    Friend WithEvents lblTitle As Label
    Friend WithEvents panelContainer As Panel
    Friend WithEvents panelContent As Panel
    Friend WithEvents lblSuccess As Label
    Friend WithEvents pictureSuccess As PictureBox
    Friend WithEvents progressbarValidating As MetroFramework.Controls.MetroProgressBar
End Class
