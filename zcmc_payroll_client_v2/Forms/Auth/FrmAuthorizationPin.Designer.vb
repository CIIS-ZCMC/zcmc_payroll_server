<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class FrmAuthorizationPin
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
        Me.GroupBox1 = New System.Windows.Forms.GroupBox()
        Me.txtAuthorizationPin = New System.Windows.Forms.TextBox()
        Me.btnImport = New System.Windows.Forms.Button()
        Me.panelDivider = New System.Windows.Forms.Panel()
        Me.panelTop = New System.Windows.Forms.Panel()
        Me.lblDeductionType = New System.Windows.Forms.Label()
        Me.panelContainer.SuspendLayout()
        Me.GroupBox1.SuspendLayout()
        Me.panelTop.SuspendLayout()
        Me.SuspendLayout()
        '
        'panelContainer
        '
        Me.panelContainer.Controls.Add(Me.GroupBox1)
        Me.panelContainer.Controls.Add(Me.panelDivider)
        Me.panelContainer.Controls.Add(Me.panelTop)
        Me.panelContainer.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContainer.Location = New System.Drawing.Point(0, 0)
        Me.panelContainer.Name = "panelContainer"
        Me.panelContainer.Size = New System.Drawing.Size(376, 188)
        Me.panelContainer.TabIndex = 2
        '
        'GroupBox1
        '
        Me.GroupBox1.Anchor = CType((((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom) _
            Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.GroupBox1.Controls.Add(Me.txtAuthorizationPin)
        Me.GroupBox1.Controls.Add(Me.btnImport)
        Me.GroupBox1.Font = New System.Drawing.Font("Segoe UI Semibold", 9.75!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.GroupBox1.Location = New System.Drawing.Point(12, 57)
        Me.GroupBox1.Name = "GroupBox1"
        Me.GroupBox1.Size = New System.Drawing.Size(352, 119)
        Me.GroupBox1.TabIndex = 19
        Me.GroupBox1.TabStop = False
        '
        'txtAuthorizationPin
        '
        Me.txtAuthorizationPin.Font = New System.Drawing.Font("Segoe UI Semibold", 15.75!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.txtAuthorizationPin.Location = New System.Drawing.Point(26, 26)
        Me.txtAuthorizationPin.Name = "txtAuthorizationPin"
        Me.txtAuthorizationPin.PasswordChar = Global.Microsoft.VisualBasic.ChrW(8226)
        Me.txtAuthorizationPin.Size = New System.Drawing.Size(307, 35)
        Me.txtAuthorizationPin.TabIndex = 23
        Me.txtAuthorizationPin.UseSystemPasswordChar = True
        '
        'btnImport
        '
        Me.btnImport.BackColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.btnImport.ForeColor = System.Drawing.Color.White
        Me.btnImport.Location = New System.Drawing.Point(26, 67)
        Me.btnImport.Name = "btnImport"
        Me.btnImport.Size = New System.Drawing.Size(307, 35)
        Me.btnImport.TabIndex = 21
        Me.btnImport.Text = "Submit"
        Me.btnImport.UseVisualStyleBackColor = False
        '
        'panelDivider
        '
        Me.panelDivider.BackColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.panelDivider.Dock = System.Windows.Forms.DockStyle.Top
        Me.panelDivider.Location = New System.Drawing.Point(0, 41)
        Me.panelDivider.Name = "panelDivider"
        Me.panelDivider.Size = New System.Drawing.Size(376, 10)
        Me.panelDivider.TabIndex = 18
        '
        'panelTop
        '
        Me.panelTop.BackColor = System.Drawing.Color.FromArgb(CType(CType(10, Byte), Integer), CType(CType(62, Byte), Integer), CType(CType(48, Byte), Integer))
        Me.panelTop.Controls.Add(Me.lblDeductionType)
        Me.panelTop.Dock = System.Windows.Forms.DockStyle.Top
        Me.panelTop.Location = New System.Drawing.Point(0, 0)
        Me.panelTop.Name = "panelTop"
        Me.panelTop.Size = New System.Drawing.Size(376, 41)
        Me.panelTop.TabIndex = 0
        '
        'lblDeductionType
        '
        Me.lblDeductionType.AutoSize = True
        Me.lblDeductionType.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblDeductionType.ForeColor = System.Drawing.Color.White
        Me.lblDeductionType.Location = New System.Drawing.Point(12, 9)
        Me.lblDeductionType.Name = "lblDeductionType"
        Me.lblDeductionType.Size = New System.Drawing.Size(151, 24)
        Me.lblDeductionType.TabIndex = 24
        Me.lblDeductionType.Text = "Authorization Pin"
        '
        'FrmAuthorizationPin
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.White
        Me.ClientSize = New System.Drawing.Size(376, 188)
        Me.Controls.Add(Me.panelContainer)
        Me.Name = "FrmAuthorizationPin"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterParent
        Me.panelContainer.ResumeLayout(False)
        Me.GroupBox1.ResumeLayout(False)
        Me.GroupBox1.PerformLayout()
        Me.panelTop.ResumeLayout(False)
        Me.panelTop.PerformLayout()
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents panelContainer As Panel
    Friend WithEvents GroupBox1 As GroupBox
    Friend WithEvents txtAuthorizationPin As TextBox
    Friend WithEvents btnImport As Button
    Friend WithEvents panelDivider As Panel
    Friend WithEvents panelTop As Panel
    Friend WithEvents lblDeductionType As Label
End Class
