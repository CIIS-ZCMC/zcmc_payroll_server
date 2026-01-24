<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class FrmSettings
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
        Dim resources As System.ComponentModel.ComponentResourceManager = New System.ComponentModel.ComponentResourceManager(GetType(FrmSettings))
        Me.panelNav = New System.Windows.Forms.Panel()
        Me.btnMinimize = New System.Windows.Forms.Button()
        Me.btnClose = New System.Windows.Forms.Button()
        Me.panelContainer = New System.Windows.Forms.Panel()
        Me.panelContent = New System.Windows.Forms.Panel()
        Me.panelBottom = New System.Windows.Forms.Panel()
        Me.Label2 = New System.Windows.Forms.Label()
        Me.panelSidebar = New System.Windows.Forms.Panel()
        Me.pnlSettingsMenu = New System.Windows.Forms.Panel()
        Me.Panel1 = New System.Windows.Forms.Panel()
        Me.btnReceivables = New System.Windows.Forms.Button()
        Me.btnDeductions = New System.Windows.Forms.Button()
        Me.btnDeductionGroups = New System.Windows.Forms.Button()
        Me.btnSettings = New System.Windows.Forms.Button()
        Me.btnLogout = New System.Windows.Forms.Button()
        Me.btnRefetchEmployee = New System.Windows.Forms.Button()
        Me.btnReports = New System.Windows.Forms.Button()
        Me.tmrSettings = New System.Windows.Forms.Timer(Me.components)
        Me.panelNav.SuspendLayout()
        Me.panelContainer.SuspendLayout()
        Me.panelBottom.SuspendLayout()
        Me.panelSidebar.SuspendLayout()
        Me.pnlSettingsMenu.SuspendLayout()
        Me.Panel1.SuspendLayout()
        Me.SuspendLayout()
        '
        'panelNav
        '
        Me.panelNav.BackColor = System.Drawing.Color.FromArgb(CType(CType(10, Byte), Integer), CType(CType(62, Byte), Integer), CType(CType(48, Byte), Integer))
        Me.panelNav.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle
        Me.panelNav.Controls.Add(Me.btnMinimize)
        Me.panelNav.Controls.Add(Me.btnClose)
        Me.panelNav.Dock = System.Windows.Forms.DockStyle.Top
        Me.panelNav.Location = New System.Drawing.Point(0, 0)
        Me.panelNav.Name = "panelNav"
        Me.panelNav.Size = New System.Drawing.Size(1081, 31)
        Me.panelNav.TabIndex = 1
        '
        'btnMinimize
        '
        Me.btnMinimize.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnMinimize.BackColor = System.Drawing.Color.Transparent
        Me.btnMinimize.BackgroundImage = CType(resources.GetObject("btnMinimize.BackgroundImage"), System.Drawing.Image)
        Me.btnMinimize.BackgroundImageLayout = System.Windows.Forms.ImageLayout.Center
        Me.btnMinimize.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnMinimize.ForeColor = System.Drawing.Color.White
        Me.btnMinimize.Location = New System.Drawing.Point(996, 3)
        Me.btnMinimize.Name = "btnMinimize"
        Me.btnMinimize.Size = New System.Drawing.Size(37, 22)
        Me.btnMinimize.TabIndex = 1
        Me.btnMinimize.UseVisualStyleBackColor = False
        '
        'btnClose
        '
        Me.btnClose.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnClose.BackColor = System.Drawing.Color.Red
        Me.btnClose.BackgroundImage = Global.zcmc_payroll_client_v2.My.Resources.Resources.close_16
        Me.btnClose.BackgroundImageLayout = System.Windows.Forms.ImageLayout.Center
        Me.btnClose.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnClose.ForeColor = System.Drawing.Color.Red
        Me.btnClose.Location = New System.Drawing.Point(1039, 3)
        Me.btnClose.Name = "btnClose"
        Me.btnClose.Size = New System.Drawing.Size(37, 22)
        Me.btnClose.TabIndex = 0
        Me.btnClose.UseVisualStyleBackColor = False
        '
        'panelContainer
        '
        Me.panelContainer.BackColor = System.Drawing.Color.White
        Me.panelContainer.Controls.Add(Me.panelContent)
        Me.panelContainer.Controls.Add(Me.panelBottom)
        Me.panelContainer.Controls.Add(Me.panelSidebar)
        Me.panelContainer.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContainer.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Pixel)
        Me.panelContainer.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        Me.panelContainer.Location = New System.Drawing.Point(0, 31)
        Me.panelContainer.Name = "panelContainer"
        Me.panelContainer.Size = New System.Drawing.Size(1081, 563)
        Me.panelContainer.TabIndex = 2
        '
        'panelContent
        '
        Me.panelContent.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle
        Me.panelContent.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContent.Font = New System.Drawing.Font("Segoe UI Semibold", 11.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.panelContent.Location = New System.Drawing.Point(223, 0)
        Me.panelContent.Name = "panelContent"
        Me.panelContent.Size = New System.Drawing.Size(858, 522)
        Me.panelContent.TabIndex = 3
        '
        'panelBottom
        '
        Me.panelBottom.BackColor = System.Drawing.Color.WhiteSmoke
        Me.panelBottom.Controls.Add(Me.Label2)
        Me.panelBottom.Dock = System.Windows.Forms.DockStyle.Bottom
        Me.panelBottom.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Pixel)
        Me.panelBottom.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        Me.panelBottom.Location = New System.Drawing.Point(223, 522)
        Me.panelBottom.Name = "panelBottom"
        Me.panelBottom.Size = New System.Drawing.Size(858, 41)
        Me.panelBottom.TabIndex = 2
        '
        'Label2
        '
        Me.Label2.Dock = System.Windows.Forms.DockStyle.Right
        Me.Label2.Font = New System.Drawing.Font("Tahoma", 12.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label2.Location = New System.Drawing.Point(704, 0)
        Me.Label2.Name = "Label2"
        Me.Label2.Size = New System.Drawing.Size(154, 41)
        Me.Label2.TabIndex = 0
        Me.Label2.Text = "Kim Horace A. Dolar"
        Me.Label2.TextAlign = System.Drawing.ContentAlignment.MiddleCenter
        '
        'panelSidebar
        '
        Me.panelSidebar.BackColor = System.Drawing.Color.FromArgb(CType(CType(10, Byte), Integer), CType(CType(62, Byte), Integer), CType(CType(48, Byte), Integer))
        Me.panelSidebar.Controls.Add(Me.pnlSettingsMenu)
        Me.panelSidebar.Controls.Add(Me.btnSettings)
        Me.panelSidebar.Controls.Add(Me.btnLogout)
        Me.panelSidebar.Controls.Add(Me.btnRefetchEmployee)
        Me.panelSidebar.Controls.Add(Me.btnReports)
        Me.panelSidebar.Dock = System.Windows.Forms.DockStyle.Left
        Me.panelSidebar.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Pixel)
        Me.panelSidebar.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        Me.panelSidebar.Location = New System.Drawing.Point(0, 0)
        Me.panelSidebar.Name = "panelSidebar"
        Me.panelSidebar.Size = New System.Drawing.Size(223, 563)
        Me.panelSidebar.TabIndex = 1
        '
        'pnlSettingsMenu
        '
        Me.pnlSettingsMenu.Controls.Add(Me.Panel1)
        Me.pnlSettingsMenu.Dock = System.Windows.Forms.DockStyle.Top
        Me.pnlSettingsMenu.Location = New System.Drawing.Point(0, 141)
        Me.pnlSettingsMenu.Name = "pnlSettingsMenu"
        Me.pnlSettingsMenu.Size = New System.Drawing.Size(223, 158)
        Me.pnlSettingsMenu.TabIndex = 12
        '
        'Panel1
        '
        Me.Panel1.Controls.Add(Me.btnReceivables)
        Me.Panel1.Controls.Add(Me.btnDeductions)
        Me.Panel1.Controls.Add(Me.btnDeductionGroups)
        Me.Panel1.Dock = System.Windows.Forms.DockStyle.Right
        Me.Panel1.Location = New System.Drawing.Point(28, 0)
        Me.Panel1.Name = "Panel1"
        Me.Panel1.Size = New System.Drawing.Size(195, 158)
        Me.Panel1.TabIndex = 0
        '
        'btnReceivables
        '
        Me.btnReceivables.Dock = System.Windows.Forms.DockStyle.Top
        Me.btnReceivables.FlatAppearance.BorderSize = 0
        Me.btnReceivables.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnReceivables.Font = New System.Drawing.Font("Microsoft Sans Serif", 12.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnReceivables.ForeColor = System.Drawing.Color.White
        Me.btnReceivables.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnReceivables.Location = New System.Drawing.Point(0, 94)
        Me.btnReceivables.Name = "btnReceivables"
        Me.btnReceivables.Size = New System.Drawing.Size(195, 47)
        Me.btnReceivables.TabIndex = 16
        Me.btnReceivables.Text = "Receivable"
        Me.btnReceivables.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnReceivables.TextImageRelation = System.Windows.Forms.TextImageRelation.ImageBeforeText
        Me.btnReceivables.UseVisualStyleBackColor = True
        '
        'btnDeductions
        '
        Me.btnDeductions.Dock = System.Windows.Forms.DockStyle.Top
        Me.btnDeductions.FlatAppearance.BorderSize = 0
        Me.btnDeductions.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnDeductions.Font = New System.Drawing.Font("Microsoft Sans Serif", 12.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnDeductions.ForeColor = System.Drawing.Color.White
        Me.btnDeductions.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnDeductions.Location = New System.Drawing.Point(0, 47)
        Me.btnDeductions.Name = "btnDeductions"
        Me.btnDeductions.Size = New System.Drawing.Size(195, 47)
        Me.btnDeductions.TabIndex = 15
        Me.btnDeductions.Text = "Deduction"
        Me.btnDeductions.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnDeductions.TextImageRelation = System.Windows.Forms.TextImageRelation.ImageBeforeText
        Me.btnDeductions.UseVisualStyleBackColor = True
        '
        'btnDeductionGroups
        '
        Me.btnDeductionGroups.Dock = System.Windows.Forms.DockStyle.Top
        Me.btnDeductionGroups.FlatAppearance.BorderSize = 0
        Me.btnDeductionGroups.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnDeductionGroups.Font = New System.Drawing.Font("Microsoft Sans Serif", 12.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnDeductionGroups.ForeColor = System.Drawing.Color.White
        Me.btnDeductionGroups.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnDeductionGroups.Location = New System.Drawing.Point(0, 0)
        Me.btnDeductionGroups.Name = "btnDeductionGroups"
        Me.btnDeductionGroups.Size = New System.Drawing.Size(195, 47)
        Me.btnDeductionGroups.TabIndex = 14
        Me.btnDeductionGroups.Text = "Deduction Group"
        Me.btnDeductionGroups.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnDeductionGroups.TextImageRelation = System.Windows.Forms.TextImageRelation.ImageBeforeText
        Me.btnDeductionGroups.UseVisualStyleBackColor = True
        '
        'btnSettings
        '
        Me.btnSettings.Dock = System.Windows.Forms.DockStyle.Top
        Me.btnSettings.Enabled = False
        Me.btnSettings.FlatAppearance.BorderSize = 0
        Me.btnSettings.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnSettings.Font = New System.Drawing.Font("Microsoft Sans Serif", 15.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnSettings.ForeColor = System.Drawing.Color.White
        Me.btnSettings.Image = Global.zcmc_payroll_client_v2.My.Resources.Resources.settings_24
        Me.btnSettings.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnSettings.Location = New System.Drawing.Point(0, 94)
        Me.btnSettings.Name = "btnSettings"
        Me.btnSettings.Size = New System.Drawing.Size(223, 47)
        Me.btnSettings.TabIndex = 11
        Me.btnSettings.Text = "Settings"
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
        Me.btnLogout.Location = New System.Drawing.Point(0, 516)
        Me.btnLogout.Name = "btnLogout"
        Me.btnLogout.Size = New System.Drawing.Size(223, 47)
        Me.btnLogout.TabIndex = 4
        Me.btnLogout.Text = " Log Out"
        Me.btnLogout.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnLogout.TextImageRelation = System.Windows.Forms.TextImageRelation.ImageBeforeText
        Me.btnLogout.UseVisualStyleBackColor = True
        '
        'btnRefetchEmployee
        '
        Me.btnRefetchEmployee.Dock = System.Windows.Forms.DockStyle.Top
        Me.btnRefetchEmployee.Enabled = False
        Me.btnRefetchEmployee.FlatAppearance.BorderSize = 0
        Me.btnRefetchEmployee.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnRefetchEmployee.Font = New System.Drawing.Font("Microsoft Sans Serif", 15.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnRefetchEmployee.ForeColor = System.Drawing.Color.White
        Me.btnRefetchEmployee.Image = Global.zcmc_payroll_client_v2.My.Resources.Resources.refetch_24
        Me.btnRefetchEmployee.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnRefetchEmployee.Location = New System.Drawing.Point(0, 47)
        Me.btnRefetchEmployee.Name = "btnRefetchEmployee"
        Me.btnRefetchEmployee.Size = New System.Drawing.Size(223, 47)
        Me.btnRefetchEmployee.TabIndex = 2
        Me.btnRefetchEmployee.Text = "Re-Fetch"
        Me.btnRefetchEmployee.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnRefetchEmployee.TextImageRelation = System.Windows.Forms.TextImageRelation.ImageBeforeText
        Me.btnRefetchEmployee.UseVisualStyleBackColor = True
        '
        'btnReports
        '
        Me.btnReports.Dock = System.Windows.Forms.DockStyle.Top
        Me.btnReports.FlatAppearance.BorderSize = 0
        Me.btnReports.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnReports.Font = New System.Drawing.Font("Microsoft Sans Serif", 15.75!)
        Me.btnReports.ForeColor = System.Drawing.Color.White
        Me.btnReports.Image = Global.zcmc_payroll_client_v2.My.Resources.Resources.report_24
        Me.btnReports.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnReports.Location = New System.Drawing.Point(0, 0)
        Me.btnReports.Name = "btnReports"
        Me.btnReports.Size = New System.Drawing.Size(223, 47)
        Me.btnReports.TabIndex = 0
        Me.btnReports.Text = "Reports"
        Me.btnReports.TextAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnReports.TextImageRelation = System.Windows.Forms.TextImageRelation.ImageBeforeText
        Me.btnReports.UseVisualStyleBackColor = True
        '
        'tmrSettings
        '
        Me.tmrSettings.Interval = 15
        '
        'FrmSettings
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.White
        Me.ClientSize = New System.Drawing.Size(1081, 594)
        Me.Controls.Add(Me.panelContainer)
        Me.Controls.Add(Me.panelNav)
        Me.FormBorderStyle = System.Windows.Forms.FormBorderStyle.None
        Me.Name = "FrmSettings"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen
        Me.WindowState = System.Windows.Forms.FormWindowState.Maximized
        Me.panelNav.ResumeLayout(False)
        Me.panelContainer.ResumeLayout(False)
        Me.panelBottom.ResumeLayout(False)
        Me.panelSidebar.ResumeLayout(False)
        Me.pnlSettingsMenu.ResumeLayout(False)
        Me.Panel1.ResumeLayout(False)
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents panelNav As Panel
    Friend WithEvents btnMinimize As Button
    Friend WithEvents btnClose As Button
    Friend WithEvents panelContainer As Panel
    Friend WithEvents panelContent As Panel
    Friend WithEvents panelBottom As Panel
    Friend WithEvents Label2 As Label
    Friend WithEvents panelSidebar As Panel
    Friend WithEvents btnLogout As Button
    Friend WithEvents btnRefetchEmployee As Button
    Friend WithEvents btnReports As Button
    Friend WithEvents pnlSettingsMenu As Panel
    Friend WithEvents btnSettings As Button
    Friend WithEvents tmrSettings As Timer
    Friend WithEvents Panel1 As Panel
    Friend WithEvents btnReceivables As Button
    Friend WithEvents btnDeductions As Button
    Friend WithEvents btnDeductionGroups As Button
End Class
