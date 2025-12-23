<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()>
Partial Class FrmMain
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
        Dim resources As System.ComponentModel.ComponentResourceManager = New System.ComponentModel.ComponentResourceManager(GetType(FrmMain))
        Me.panelContainer = New System.Windows.Forms.Panel()
        Me.panelContent = New System.Windows.Forms.Panel()
        Me.panelBottom = New System.Windows.Forms.Panel()
        Me.Label2 = New System.Windows.Forms.Label()
        Me.panelSidebar = New System.Windows.Forms.Panel()
        Me.btnSettings = New System.Windows.Forms.Button()
        Me.btnLogout = New System.Windows.Forms.Button()
        Me.btnGeneralPayroll = New System.Windows.Forms.Button()
        Me.btnEmployee = New System.Windows.Forms.Button()
        Me.btnImports = New System.Windows.Forms.Button()
        Me.btnDashboard = New System.Windows.Forms.Button()
        Me.panelNavbar = New System.Windows.Forms.Panel()
        Me.Label3 = New System.Windows.Forms.Label()
        Me.btnSet = New System.Windows.Forms.Button()
        Me.Label1 = New System.Windows.Forms.Label()
        Me.PictureBox1 = New System.Windows.Forms.PictureBox()
        Me.TabPage1 = New System.Windows.Forms.TabPage()
        Me.TabPage2 = New System.Windows.Forms.TabPage()
        Me.panelContainer.SuspendLayout()
        Me.panelBottom.SuspendLayout()
        Me.panelSidebar.SuspendLayout()
        Me.panelNavbar.SuspendLayout()
        CType(Me.PictureBox1, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.SuspendLayout()
        '
        'panelContainer
        '
        Me.panelContainer.BackColor = System.Drawing.Color.White
        Me.panelContainer.Controls.Add(Me.panelContent)
        Me.panelContainer.Controls.Add(Me.panelBottom)
        Me.panelContainer.Controls.Add(Me.panelSidebar)
        Me.panelContainer.Controls.Add(Me.panelNavbar)
        Me.panelContainer.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContainer.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Pixel)
        Me.panelContainer.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        Me.panelContainer.Location = New System.Drawing.Point(0, 0)
        Me.panelContainer.Name = "panelContainer"
        Me.panelContainer.Size = New System.Drawing.Size(1089, 554)
        Me.panelContainer.TabIndex = 1
        '
        'panelContent
        '
        Me.panelContent.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle
        Me.panelContent.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContent.Font = New System.Drawing.Font("Tahoma", 14.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.panelContent.Location = New System.Drawing.Point(223, 54)
        Me.panelContent.Name = "panelContent"
        Me.panelContent.Size = New System.Drawing.Size(866, 459)
        Me.panelContent.TabIndex = 3
        '
        'panelBottom
        '
        Me.panelBottom.BackColor = System.Drawing.Color.WhiteSmoke
        Me.panelBottom.Controls.Add(Me.Label2)
        Me.panelBottom.Dock = System.Windows.Forms.DockStyle.Bottom
        Me.panelBottom.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Pixel)
        Me.panelBottom.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        Me.panelBottom.Location = New System.Drawing.Point(223, 513)
        Me.panelBottom.Name = "panelBottom"
        Me.panelBottom.Size = New System.Drawing.Size(866, 41)
        Me.panelBottom.TabIndex = 2
        '
        'Label2
        '
        Me.Label2.Dock = System.Windows.Forms.DockStyle.Right
        Me.Label2.Font = New System.Drawing.Font("Tahoma", 12.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label2.Location = New System.Drawing.Point(712, 0)
        Me.Label2.Name = "Label2"
        Me.Label2.Size = New System.Drawing.Size(154, 41)
        Me.Label2.TabIndex = 0
        Me.Label2.Text = "Kim Horace A. Dolar"
        Me.Label2.TextAlign = System.Drawing.ContentAlignment.MiddleCenter
        '
        'panelSidebar
        '
        Me.panelSidebar.BackColor = System.Drawing.Color.SteelBlue
        Me.panelSidebar.Controls.Add(Me.btnSettings)
        Me.panelSidebar.Controls.Add(Me.btnLogout)
        Me.panelSidebar.Controls.Add(Me.btnGeneralPayroll)
        Me.panelSidebar.Controls.Add(Me.btnEmployee)
        Me.panelSidebar.Controls.Add(Me.btnImports)
        Me.panelSidebar.Controls.Add(Me.btnDashboard)
        Me.panelSidebar.Dock = System.Windows.Forms.DockStyle.Left
        Me.panelSidebar.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Pixel)
        Me.panelSidebar.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        Me.panelSidebar.Location = New System.Drawing.Point(0, 54)
        Me.panelSidebar.Name = "panelSidebar"
        Me.panelSidebar.Size = New System.Drawing.Size(223, 500)
        Me.panelSidebar.TabIndex = 1
        '
        'btnSettings
        '
        Me.btnSettings.Dock = System.Windows.Forms.DockStyle.Bottom
        Me.btnSettings.FlatAppearance.BorderSize = 0
        Me.btnSettings.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnSettings.Font = New System.Drawing.Font("Microsoft Sans Serif", 15.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnSettings.ForeColor = System.Drawing.Color.White
        Me.btnSettings.Image = Global.zcmc_payroll_client_v2.My.Resources.Resources.settings_24
        Me.btnSettings.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnSettings.Location = New System.Drawing.Point(0, 406)
        Me.btnSettings.Name = "btnSettings"
        Me.btnSettings.Size = New System.Drawing.Size(223, 47)
        Me.btnSettings.TabIndex = 5
        Me.btnSettings.Text = " Settings"
        Me.btnSettings.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnSettings.TextImageRelation = System.Windows.Forms.TextImageRelation.ImageBeforeText
        Me.btnSettings.UseVisualStyleBackColor = True
        '
        'btnLogout
        '
        Me.btnLogout.Dock = System.Windows.Forms.DockStyle.Bottom
        Me.btnLogout.FlatAppearance.BorderSize = 0
        Me.btnLogout.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnLogout.Font = New System.Drawing.Font("Microsoft Sans Serif", 15.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnLogout.ForeColor = System.Drawing.Color.White
        Me.btnLogout.Image = Global.zcmc_payroll_client_v2.My.Resources.Resources.sign_out_24
        Me.btnLogout.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnLogout.Location = New System.Drawing.Point(0, 453)
        Me.btnLogout.Name = "btnLogout"
        Me.btnLogout.Size = New System.Drawing.Size(223, 47)
        Me.btnLogout.TabIndex = 4
        Me.btnLogout.Text = " Log Out"
        Me.btnLogout.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnLogout.TextImageRelation = System.Windows.Forms.TextImageRelation.ImageBeforeText
        Me.btnLogout.UseVisualStyleBackColor = True
        '
        'btnGeneralPayroll
        '
        Me.btnGeneralPayroll.Dock = System.Windows.Forms.DockStyle.Top
        Me.btnGeneralPayroll.FlatAppearance.BorderSize = 0
        Me.btnGeneralPayroll.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnGeneralPayroll.Font = New System.Drawing.Font("Microsoft Sans Serif", 15.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnGeneralPayroll.ForeColor = System.Drawing.Color.White
        Me.btnGeneralPayroll.Image = Global.zcmc_payroll_client_v2.My.Resources.Resources.payroll_24
        Me.btnGeneralPayroll.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnGeneralPayroll.Location = New System.Drawing.Point(0, 141)
        Me.btnGeneralPayroll.Name = "btnGeneralPayroll"
        Me.btnGeneralPayroll.Size = New System.Drawing.Size(223, 47)
        Me.btnGeneralPayroll.TabIndex = 3
        Me.btnGeneralPayroll.Text = " General Payroll"
        Me.btnGeneralPayroll.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnGeneralPayroll.TextImageRelation = System.Windows.Forms.TextImageRelation.ImageBeforeText
        Me.btnGeneralPayroll.UseVisualStyleBackColor = True
        '
        'btnEmployee
        '
        Me.btnEmployee.Dock = System.Windows.Forms.DockStyle.Top
        Me.btnEmployee.FlatAppearance.BorderSize = 0
        Me.btnEmployee.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnEmployee.Font = New System.Drawing.Font("Microsoft Sans Serif", 15.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnEmployee.ForeColor = System.Drawing.Color.White
        Me.btnEmployee.Image = Global.zcmc_payroll_client_v2.My.Resources.Resources.employee_24
        Me.btnEmployee.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnEmployee.Location = New System.Drawing.Point(0, 94)
        Me.btnEmployee.Name = "btnEmployee"
        Me.btnEmployee.Size = New System.Drawing.Size(223, 47)
        Me.btnEmployee.TabIndex = 2
        Me.btnEmployee.Text = " Employees"
        Me.btnEmployee.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnEmployee.TextImageRelation = System.Windows.Forms.TextImageRelation.ImageBeforeText
        Me.btnEmployee.UseVisualStyleBackColor = True
        '
        'btnImports
        '
        Me.btnImports.Dock = System.Windows.Forms.DockStyle.Top
        Me.btnImports.FlatAppearance.BorderSize = 0
        Me.btnImports.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnImports.Font = New System.Drawing.Font("Microsoft Sans Serif", 15.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnImports.ForeColor = System.Drawing.Color.White
        Me.btnImports.Image = CType(resources.GetObject("btnImports.Image"), System.Drawing.Image)
        Me.btnImports.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnImports.Location = New System.Drawing.Point(0, 47)
        Me.btnImports.Name = "btnImports"
        Me.btnImports.Size = New System.Drawing.Size(223, 47)
        Me.btnImports.TabIndex = 1
        Me.btnImports.Text = " Imports"
        Me.btnImports.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnImports.TextImageRelation = System.Windows.Forms.TextImageRelation.ImageBeforeText
        Me.btnImports.UseVisualStyleBackColor = True
        '
        'btnDashboard
        '
        Me.btnDashboard.Dock = System.Windows.Forms.DockStyle.Top
        Me.btnDashboard.FlatAppearance.BorderSize = 0
        Me.btnDashboard.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnDashboard.Font = New System.Drawing.Font("Microsoft Sans Serif", 15.75!)
        Me.btnDashboard.ForeColor = System.Drawing.Color.White
        Me.btnDashboard.Image = Global.zcmc_payroll_client_v2.My.Resources.Resources.dashboard_24
        Me.btnDashboard.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnDashboard.Location = New System.Drawing.Point(0, 0)
        Me.btnDashboard.Name = "btnDashboard"
        Me.btnDashboard.Size = New System.Drawing.Size(223, 47)
        Me.btnDashboard.TabIndex = 0
        Me.btnDashboard.Text = " Dashboard"
        Me.btnDashboard.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnDashboard.TextImageRelation = System.Windows.Forms.TextImageRelation.ImageBeforeText
        Me.btnDashboard.UseVisualStyleBackColor = True
        '
        'panelNavbar
        '
        Me.panelNavbar.BackColor = System.Drawing.Color.White
        Me.panelNavbar.Controls.Add(Me.Label3)
        Me.panelNavbar.Controls.Add(Me.btnSet)
        Me.panelNavbar.Controls.Add(Me.Label1)
        Me.panelNavbar.Controls.Add(Me.PictureBox1)
        Me.panelNavbar.Dock = System.Windows.Forms.DockStyle.Top
        Me.panelNavbar.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Pixel)
        Me.panelNavbar.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        Me.panelNavbar.Location = New System.Drawing.Point(0, 0)
        Me.panelNavbar.Name = "panelNavbar"
        Me.panelNavbar.Size = New System.Drawing.Size(1089, 54)
        Me.panelNavbar.TabIndex = 0
        '
        'Label3
        '
        Me.Label3.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.Label3.AutoSize = True
        Me.Label3.Font = New System.Drawing.Font("Tahoma", 15.75!, System.Drawing.FontStyle.Underline, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label3.ForeColor = System.Drawing.Color.Black
        Me.Label3.Location = New System.Drawing.Point(835, 20)
        Me.Label3.Name = "Label3"
        Me.Label3.Size = New System.Drawing.Size(158, 25)
        Me.Label3.TabIndex = 6
        Me.Label3.Text = "December 2025"
        '
        'btnSet
        '
        Me.btnSet.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnSet.FlatAppearance.BorderColor = System.Drawing.Color.SteelBlue
        Me.btnSet.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnSet.Location = New System.Drawing.Point(999, 20)
        Me.btnSet.Name = "btnSet"
        Me.btnSet.Size = New System.Drawing.Size(78, 25)
        Me.btnSet.TabIndex = 4
        Me.btnSet.Text = "Change"
        Me.btnSet.UseVisualStyleBackColor = True
        '
        'Label1
        '
        Me.Label1.AutoSize = True
        Me.Label1.Font = New System.Drawing.Font("Tahoma", 20.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label1.ForeColor = System.Drawing.Color.SteelBlue
        Me.Label1.Location = New System.Drawing.Point(60, 12)
        Me.Label1.Name = "Label1"
        Me.Label1.Size = New System.Drawing.Size(588, 33)
        Me.Label1.TabIndex = 3
        Me.Label1.Text = "Zamboanga City Medical Center : Payroll System"
        '
        'PictureBox1
        '
        Me.PictureBox1.BackgroundImage = Global.zcmc_payroll_client_v2.My.Resources.Resources.zcmc_logo
        Me.PictureBox1.BackgroundImageLayout = System.Windows.Forms.ImageLayout.Stretch
        Me.PictureBox1.Location = New System.Drawing.Point(12, 4)
        Me.PictureBox1.Name = "PictureBox1"
        Me.PictureBox1.Size = New System.Drawing.Size(42, 48)
        Me.PictureBox1.TabIndex = 2
        Me.PictureBox1.TabStop = False
        '
        'TabPage1
        '
        Me.TabPage1.Location = New System.Drawing.Point(0, 0)
        Me.TabPage1.Name = "TabPage1"
        Me.TabPage1.Size = New System.Drawing.Size(200, 100)
        Me.TabPage1.TabIndex = 0
        '
        'TabPage2
        '
        Me.TabPage2.Location = New System.Drawing.Point(0, 0)
        Me.TabPage2.Name = "TabPage2"
        Me.TabPage2.Size = New System.Drawing.Size(200, 100)
        Me.TabPage2.TabIndex = 0
        '
        'FrmMain
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.White
        Me.ClientSize = New System.Drawing.Size(1089, 554)
        Me.Controls.Add(Me.panelContainer)
        Me.DoubleBuffered = True
        Me.Font = New System.Drawing.Font("Tahoma", 8.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        Me.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedSingle
        Me.Name = "FrmMain"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterParent
        Me.WindowState = System.Windows.Forms.FormWindowState.Maximized
        Me.panelContainer.ResumeLayout(False)
        Me.panelBottom.ResumeLayout(False)
        Me.panelSidebar.ResumeLayout(False)
        Me.panelNavbar.ResumeLayout(False)
        Me.panelNavbar.PerformLayout()
        CType(Me.PictureBox1, System.ComponentModel.ISupportInitialize).EndInit()
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents panelContainer As Panel
    Friend WithEvents panelNavbar As Panel
    Friend WithEvents panelSidebar As Panel
    Friend WithEvents panelBottom As Panel
    Friend WithEvents PictureBox1 As PictureBox
    Friend WithEvents Label1 As Label
    Friend WithEvents btnDashboard As Button
    Friend WithEvents btnImports As Button
    Friend WithEvents btnSettings As Button
    Friend WithEvents btnLogout As Button
    Friend WithEvents btnGeneralPayroll As Button
    Friend WithEvents btnEmployee As Button
    Friend WithEvents panelContent As Panel
    Friend WithEvents btnSet As Button
    Friend WithEvents Label3 As Label
    Friend WithEvents Label2 As Label
    Friend WithEvents TabPage1 As TabPage
    Friend WithEvents TabPage2 As TabPage
End Class
