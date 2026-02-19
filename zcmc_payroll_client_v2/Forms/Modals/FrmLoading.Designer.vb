<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class FrmLoading
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
        Me.components = New System.ComponentModel.Container()
        Me.panelContainer = New System.Windows.Forms.Panel()
        Me.panelContent = New System.Windows.Forms.Panel()
        Me.pbLoading = New System.Windows.Forms.ProgressBar()
        Me.lblMessage = New System.Windows.Forms.Label()
        Me.pictureSuccess = New System.Windows.Forms.PictureBox()
        Me.TimerLoading = New System.Windows.Forms.Timer(Me.components)
        Me.panelContainer.SuspendLayout()
        Me.panelContent.SuspendLayout()
        CType(Me.pictureSuccess, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.SuspendLayout()
        '
        'panelContainer
        '
        Me.panelContainer.Controls.Add(Me.panelContent)
        Me.panelContainer.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContainer.Location = New System.Drawing.Point(0, 0)
        Me.panelContainer.Name = "panelContainer"
        Me.panelContainer.Size = New System.Drawing.Size(451, 83)
        Me.panelContainer.TabIndex = 3
        '
        'panelContent
        '
        Me.panelContent.Controls.Add(Me.pbLoading)
        Me.panelContent.Controls.Add(Me.lblMessage)
        Me.panelContent.Controls.Add(Me.pictureSuccess)
        Me.panelContent.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContent.Location = New System.Drawing.Point(0, 0)
        Me.panelContent.Name = "panelContent"
        Me.panelContent.Size = New System.Drawing.Size(451, 83)
        Me.panelContent.TabIndex = 33
        '
        'pbLoading
        '
        Me.pbLoading.BackColor = System.Drawing.Color.WhiteSmoke
        Me.pbLoading.Dock = System.Windows.Forms.DockStyle.Top
        Me.pbLoading.ForeColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.pbLoading.Location = New System.Drawing.Point(0, 0)
        Me.pbLoading.MarqueeAnimationSpeed = 30
        Me.pbLoading.Name = "pbLoading"
        Me.pbLoading.Size = New System.Drawing.Size(451, 23)
        Me.pbLoading.TabIndex = 24
        Me.pbLoading.Value = 10
        '
        'lblMessage
        '
        Me.lblMessage.Anchor = System.Windows.Forms.AnchorStyles.Top
        Me.lblMessage.AutoSize = True
        Me.lblMessage.Font = New System.Drawing.Font("Segoe UI Semibold", 18.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblMessage.ForeColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.lblMessage.Location = New System.Drawing.Point(69, 33)
        Me.lblMessage.Name = "lblMessage"
        Me.lblMessage.Size = New System.Drawing.Size(344, 32)
        Me.lblMessage.TabIndex = 23
        Me.lblMessage.Text = "Payroll Generated Successfully" & Global.Microsoft.VisualBasic.ChrW(13) & Global.Microsoft.VisualBasic.ChrW(10)
        '
        'pictureSuccess
        '
        Me.pictureSuccess.Anchor = System.Windows.Forms.AnchorStyles.Top
        Me.pictureSuccess.BackColor = System.Drawing.Color.White
        Me.pictureSuccess.BackgroundImage = Global.zcmc_payroll_client_v2.My.Resources.Resources.approved_32
        Me.pictureSuccess.BackgroundImageLayout = System.Windows.Forms.ImageLayout.Stretch
        Me.pictureSuccess.Location = New System.Drawing.Point(20, 29)
        Me.pictureSuccess.Name = "pictureSuccess"
        Me.pictureSuccess.Size = New System.Drawing.Size(43, 40)
        Me.pictureSuccess.TabIndex = 22
        Me.pictureSuccess.TabStop = False
        '
        'FrmLoading
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.ClientSize = New System.Drawing.Size(451, 83)
        Me.Controls.Add(Me.panelContainer)
        Me.FormBorderStyle = System.Windows.Forms.FormBorderStyle.None
        Me.Name = "FrmLoading"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen
        Me.Text = "FrmLoading"
        Me.panelContainer.ResumeLayout(False)
        Me.panelContent.ResumeLayout(False)
        Me.panelContent.PerformLayout()
        CType(Me.pictureSuccess, System.ComponentModel.ISupportInitialize).EndInit()
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents panelContainer As Panel
    Friend WithEvents panelContent As Panel
    Friend WithEvents lblMessage As Label
    Friend WithEvents pictureSuccess As PictureBox
    Friend WithEvents pbLoading As ProgressBar
    Friend WithEvents TimerLoading As Timer
End Class
