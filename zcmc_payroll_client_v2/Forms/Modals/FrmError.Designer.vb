<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class FrmError
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
        Me.lblSuccess = New System.Windows.Forms.Label()
        Me.panelBottom = New System.Windows.Forms.Panel()
        Me.btnOk = New System.Windows.Forms.Button()
        Me.panelTop = New System.Windows.Forms.Panel()
        Me.lblTitle = New System.Windows.Forms.Label()
        Me.pictureSuccess = New System.Windows.Forms.PictureBox()
        Me.panelContainer.SuspendLayout()
        Me.panelContent.SuspendLayout()
        Me.panelBottom.SuspendLayout()
        Me.panelTop.SuspendLayout()
        CType(Me.pictureSuccess, System.ComponentModel.ISupportInitialize).BeginInit()
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
        Me.panelContainer.Size = New System.Drawing.Size(471, 172)
        Me.panelContainer.TabIndex = 6
        '
        'panelContent
        '
        Me.panelContent.Controls.Add(Me.lblSuccess)
        Me.panelContent.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContent.Location = New System.Drawing.Point(0, 60)
        Me.panelContent.Name = "panelContent"
        Me.panelContent.Size = New System.Drawing.Size(471, 68)
        Me.panelContent.TabIndex = 36
        '
        'lblSuccess
        '
        Me.lblSuccess.Dock = System.Windows.Forms.DockStyle.Fill
        Me.lblSuccess.Font = New System.Drawing.Font("Segoe UI Semibold", 12.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblSuccess.ForeColor = System.Drawing.Color.Black
        Me.lblSuccess.Location = New System.Drawing.Point(0, 0)
        Me.lblSuccess.Name = "lblSuccess"
        Me.lblSuccess.Size = New System.Drawing.Size(471, 68)
        Me.lblSuccess.TabIndex = 23
        Me.lblSuccess.Text = "Payroll Generated Successfully" & Global.Microsoft.VisualBasic.ChrW(13) & Global.Microsoft.VisualBasic.ChrW(10)
        '
        'panelBottom
        '
        Me.panelBottom.BackColor = System.Drawing.Color.WhiteSmoke
        Me.panelBottom.Controls.Add(Me.btnOk)
        Me.panelBottom.Dock = System.Windows.Forms.DockStyle.Bottom
        Me.panelBottom.Location = New System.Drawing.Point(0, 128)
        Me.panelBottom.Name = "panelBottom"
        Me.panelBottom.Size = New System.Drawing.Size(471, 44)
        Me.panelBottom.TabIndex = 35
        '
        'btnOk
        '
        Me.btnOk.BackColor = System.Drawing.Color.White
        Me.btnOk.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnOk.Font = New System.Drawing.Font("Segoe UI", 12.0!)
        Me.btnOk.ForeColor = System.Drawing.Color.Black
        Me.btnOk.Location = New System.Drawing.Point(161, 6)
        Me.btnOk.Name = "btnOk"
        Me.btnOk.Size = New System.Drawing.Size(148, 33)
        Me.btnOk.TabIndex = 5
        Me.btnOk.Text = "OK"
        Me.btnOk.UseVisualStyleBackColor = False
        '
        'panelTop
        '
        Me.panelTop.BackColor = System.Drawing.Color.IndianRed
        Me.panelTop.Controls.Add(Me.lblTitle)
        Me.panelTop.Controls.Add(Me.pictureSuccess)
        Me.panelTop.Dock = System.Windows.Forms.DockStyle.Top
        Me.panelTop.Location = New System.Drawing.Point(0, 0)
        Me.panelTop.Name = "panelTop"
        Me.panelTop.Size = New System.Drawing.Size(471, 60)
        Me.panelTop.TabIndex = 32
        '
        'lblTitle
        '
        Me.lblTitle.Anchor = System.Windows.Forms.AnchorStyles.Top
        Me.lblTitle.AutoSize = True
        Me.lblTitle.Font = New System.Drawing.Font("Segoe UI Semibold", 18.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblTitle.ForeColor = System.Drawing.Color.White
        Me.lblTitle.Location = New System.Drawing.Point(61, 16)
        Me.lblTitle.Name = "lblTitle"
        Me.lblTitle.Size = New System.Drawing.Size(143, 32)
        Me.lblTitle.TabIndex = 2
        Me.lblTitle.Text = "Information"
        '
        'pictureSuccess
        '
        Me.pictureSuccess.Anchor = System.Windows.Forms.AnchorStyles.Top
        Me.pictureSuccess.BackColor = System.Drawing.Color.Transparent
        Me.pictureSuccess.BackgroundImage = Global.zcmc_payroll_client_v2.My.Resources.Resources.approved_32
        Me.pictureSuccess.BackgroundImageLayout = System.Windows.Forms.ImageLayout.Stretch
        Me.pictureSuccess.Location = New System.Drawing.Point(12, 12)
        Me.pictureSuccess.Name = "pictureSuccess"
        Me.pictureSuccess.Size = New System.Drawing.Size(43, 40)
        Me.pictureSuccess.TabIndex = 22
        Me.pictureSuccess.TabStop = False
        '
        'FrmError
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.ClientSize = New System.Drawing.Size(471, 172)
        Me.Controls.Add(Me.panelContainer)
        Me.FormBorderStyle = System.Windows.Forms.FormBorderStyle.None
        Me.Name = "FrmError"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterParent
        Me.Text = "FrmError"
        Me.panelContainer.ResumeLayout(False)
        Me.panelContent.ResumeLayout(False)
        Me.panelBottom.ResumeLayout(False)
        Me.panelTop.ResumeLayout(False)
        Me.panelTop.PerformLayout()
        CType(Me.pictureSuccess, System.ComponentModel.ISupportInitialize).EndInit()
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents panelContainer As Panel
    Friend WithEvents panelContent As Panel
    Friend WithEvents lblSuccess As Label
    Friend WithEvents panelBottom As Panel
    Friend WithEvents btnOk As Button
    Friend WithEvents panelTop As Panel
    Friend WithEvents lblTitle As Label
    Friend WithEvents pictureSuccess As PictureBox
End Class
