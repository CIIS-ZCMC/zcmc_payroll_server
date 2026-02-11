Imports MaterialSkin.Controls

<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()>
Partial Class FrmStepper
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
        Dim resources As System.ComponentModel.ComponentResourceManager = New System.ComponentModel.ComponentResourceManager(GetType(FrmStepper))
        Me.panelContainer = New System.Windows.Forms.Panel()
        Me.pnlContent = New System.Windows.Forms.Panel()
        Me.pbLoading = New System.Windows.Forms.ProgressBar()
        Me.pnlUserControlDisplay = New System.Windows.Forms.Panel()
        Me.panelFooter = New System.Windows.Forms.Panel()
        Me.btnNext = New System.Windows.Forms.Button()
        Me.btnBack = New System.Windows.Forms.Button()
        Me.panelStepper = New System.Windows.Forms.Panel()
        Me.tblpStepper = New System.Windows.Forms.TableLayoutPanel()
        Me.panelNavbar = New System.Windows.Forms.Panel()
        Me.btnMore = New System.Windows.Forms.Button()
        Me.lblMonthYear = New System.Windows.Forms.Label()
        Me.btnSet = New System.Windows.Forms.Button()
        Me.lblTitle = New System.Windows.Forms.Label()
        Me.PictureBox1 = New System.Windows.Forms.PictureBox()
        Me.Panel1 = New System.Windows.Forms.Panel()
        Me.btnMinimize = New System.Windows.Forms.Button()
        Me.btnMaximize = New System.Windows.Forms.Button()
        Me.panelContainer.SuspendLayout()
        Me.pnlContent.SuspendLayout()
        Me.panelFooter.SuspendLayout()
        Me.panelStepper.SuspendLayout()
        Me.panelNavbar.SuspendLayout()
        CType(Me.PictureBox1, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.Panel1.SuspendLayout()
        Me.SuspendLayout()
        '
        'panelContainer
        '
        Me.panelContainer.BackColor = System.Drawing.Color.White
        Me.panelContainer.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle
        Me.panelContainer.Controls.Add(Me.pnlContent)
        Me.panelContainer.Controls.Add(Me.panelFooter)
        Me.panelContainer.Controls.Add(Me.panelStepper)
        Me.panelContainer.Controls.Add(Me.panelNavbar)
        Me.panelContainer.Controls.Add(Me.Panel1)
        Me.panelContainer.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContainer.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Pixel)
        Me.panelContainer.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        Me.panelContainer.Location = New System.Drawing.Point(0, 0)
        Me.panelContainer.Name = "panelContainer"
        Me.panelContainer.Size = New System.Drawing.Size(1338, 750)
        Me.panelContainer.TabIndex = 1
        '
        'pnlContent
        '
        Me.pnlContent.BackColor = System.Drawing.Color.WhiteSmoke
        Me.pnlContent.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle
        Me.pnlContent.Controls.Add(Me.pbLoading)
        Me.pnlContent.Controls.Add(Me.pnlUserControlDisplay)
        Me.pnlContent.Dock = System.Windows.Forms.DockStyle.Fill
        Me.pnlContent.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Pixel)
        Me.pnlContent.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        Me.pnlContent.Location = New System.Drawing.Point(0, 158)
        Me.pnlContent.Name = "pnlContent"
        Me.pnlContent.Size = New System.Drawing.Size(1336, 540)
        Me.pnlContent.TabIndex = 7
        '
        'pbLoading
        '
        Me.pbLoading.BackColor = System.Drawing.Color.WhiteSmoke
        Me.pbLoading.Dock = System.Windows.Forms.DockStyle.Top
        Me.pbLoading.ForeColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.pbLoading.Location = New System.Drawing.Point(0, 0)
        Me.pbLoading.MarqueeAnimationSpeed = 30
        Me.pbLoading.Name = "pbLoading"
        Me.pbLoading.Size = New System.Drawing.Size(1334, 23)
        Me.pbLoading.TabIndex = 0
        Me.pbLoading.Value = 10
        Me.pbLoading.Visible = False
        '
        'pnlUserControlDisplay
        '
        Me.pnlUserControlDisplay.Anchor = CType((((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom) _
            Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.pnlUserControlDisplay.BackColor = System.Drawing.Color.White
        Me.pnlUserControlDisplay.Location = New System.Drawing.Point(11, 29)
        Me.pnlUserControlDisplay.Name = "pnlUserControlDisplay"
        Me.pnlUserControlDisplay.Size = New System.Drawing.Size(1312, 493)
        Me.pnlUserControlDisplay.TabIndex = 0
        '
        'panelFooter
        '
        Me.panelFooter.BackColor = System.Drawing.Color.White
        Me.panelFooter.Controls.Add(Me.btnNext)
        Me.panelFooter.Controls.Add(Me.btnBack)
        Me.panelFooter.Dock = System.Windows.Forms.DockStyle.Bottom
        Me.panelFooter.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Pixel)
        Me.panelFooter.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        Me.panelFooter.Location = New System.Drawing.Point(0, 698)
        Me.panelFooter.Name = "panelFooter"
        Me.panelFooter.Size = New System.Drawing.Size(1336, 50)
        Me.panelFooter.TabIndex = 6
        '
        'btnNext
        '
        Me.btnNext.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnNext.BackColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.btnNext.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnNext.Font = New System.Drawing.Font("Tahoma", 14.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnNext.ForeColor = System.Drawing.Color.White
        Me.btnNext.Location = New System.Drawing.Point(1223, 6)
        Me.btnNext.Name = "btnNext"
        Me.btnNext.Size = New System.Drawing.Size(101, 32)
        Me.btnNext.TabIndex = 1
        Me.btnNext.Text = "Next"
        Me.btnNext.UseVisualStyleBackColor = False
        '
        'btnBack
        '
        Me.btnBack.BackColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.btnBack.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnBack.Font = New System.Drawing.Font("Tahoma", 14.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnBack.ForeColor = System.Drawing.Color.White
        Me.btnBack.Location = New System.Drawing.Point(12, 6)
        Me.btnBack.Name = "btnBack"
        Me.btnBack.Size = New System.Drawing.Size(101, 32)
        Me.btnBack.TabIndex = 0
        Me.btnBack.Text = "Back"
        Me.btnBack.UseVisualStyleBackColor = False
        '
        'panelStepper
        '
        Me.panelStepper.BackColor = System.Drawing.Color.White
        Me.panelStepper.Controls.Add(Me.tblpStepper)
        Me.panelStepper.Dock = System.Windows.Forms.DockStyle.Top
        Me.panelStepper.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Pixel)
        Me.panelStepper.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        Me.panelStepper.Location = New System.Drawing.Point(0, 91)
        Me.panelStepper.Name = "panelStepper"
        Me.panelStepper.Size = New System.Drawing.Size(1336, 67)
        Me.panelStepper.TabIndex = 5
        '
        'tblpStepper
        '
        Me.tblpStepper.BackColor = System.Drawing.Color.White
        Me.tblpStepper.CellBorderStyle = System.Windows.Forms.TableLayoutPanelCellBorderStyle.[Single]
        Me.tblpStepper.ColumnCount = 8
        Me.tblpStepper.ColumnStyles.Add(New System.Windows.Forms.ColumnStyle(System.Windows.Forms.SizeType.Percent, 12.5!))
        Me.tblpStepper.ColumnStyles.Add(New System.Windows.Forms.ColumnStyle(System.Windows.Forms.SizeType.Percent, 12.5!))
        Me.tblpStepper.ColumnStyles.Add(New System.Windows.Forms.ColumnStyle(System.Windows.Forms.SizeType.Percent, 12.5!))
        Me.tblpStepper.ColumnStyles.Add(New System.Windows.Forms.ColumnStyle(System.Windows.Forms.SizeType.Percent, 12.5!))
        Me.tblpStepper.ColumnStyles.Add(New System.Windows.Forms.ColumnStyle(System.Windows.Forms.SizeType.Percent, 12.5!))
        Me.tblpStepper.ColumnStyles.Add(New System.Windows.Forms.ColumnStyle(System.Windows.Forms.SizeType.Percent, 12.5!))
        Me.tblpStepper.ColumnStyles.Add(New System.Windows.Forms.ColumnStyle(System.Windows.Forms.SizeType.Percent, 12.5!))
        Me.tblpStepper.ColumnStyles.Add(New System.Windows.Forms.ColumnStyle(System.Windows.Forms.SizeType.Percent, 12.5!))
        Me.tblpStepper.Dock = System.Windows.Forms.DockStyle.Fill
        Me.tblpStepper.Location = New System.Drawing.Point(0, 0)
        Me.tblpStepper.Name = "tblpStepper"
        Me.tblpStepper.RowCount = 1
        Me.tblpStepper.RowStyles.Add(New System.Windows.Forms.RowStyle(System.Windows.Forms.SizeType.Percent, 100.0!))
        Me.tblpStepper.Size = New System.Drawing.Size(1336, 67)
        Me.tblpStepper.TabIndex = 0
        '
        'panelNavbar
        '
        Me.panelNavbar.BackColor = System.Drawing.Color.FromArgb(CType(CType(10, Byte), Integer), CType(CType(62, Byte), Integer), CType(CType(48, Byte), Integer))
        Me.panelNavbar.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle
        Me.panelNavbar.Controls.Add(Me.btnMore)
        Me.panelNavbar.Controls.Add(Me.lblMonthYear)
        Me.panelNavbar.Controls.Add(Me.btnSet)
        Me.panelNavbar.Controls.Add(Me.lblTitle)
        Me.panelNavbar.Controls.Add(Me.PictureBox1)
        Me.panelNavbar.Dock = System.Windows.Forms.DockStyle.Top
        Me.panelNavbar.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Pixel)
        Me.panelNavbar.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        Me.panelNavbar.Location = New System.Drawing.Point(0, 31)
        Me.panelNavbar.Name = "panelNavbar"
        Me.panelNavbar.Size = New System.Drawing.Size(1336, 60)
        Me.panelNavbar.TabIndex = 4
        '
        'btnMore
        '
        Me.btnMore.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnMore.BackColor = System.Drawing.Color.White
        Me.btnMore.FlatAppearance.BorderColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.btnMore.FlatAppearance.BorderSize = 2
        Me.btnMore.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnMore.Font = New System.Drawing.Font("Tahoma", 14.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnMore.ForeColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.btnMore.Location = New System.Drawing.Point(1232, 14)
        Me.btnMore.Name = "btnMore"
        Me.btnMore.Size = New System.Drawing.Size(99, 33)
        Me.btnMore.TabIndex = 9
        Me.btnMore.Text = "More"
        Me.btnMore.UseVisualStyleBackColor = False
        '
        'lblMonthYear
        '
        Me.lblMonthYear.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.lblMonthYear.BackColor = System.Drawing.Color.Transparent
        Me.lblMonthYear.Font = New System.Drawing.Font("Tahoma", 15.75!, System.Drawing.FontStyle.Underline)
        Me.lblMonthYear.ForeColor = System.Drawing.Color.White
        Me.lblMonthYear.Location = New System.Drawing.Point(774, 18)
        Me.lblMonthYear.Name = "lblMonthYear"
        Me.lblMonthYear.Size = New System.Drawing.Size(347, 25)
        Me.lblMonthYear.TabIndex = 8
        Me.lblMonthYear.Text = "December 2025 : First Half"
        Me.lblMonthYear.TextAlign = System.Drawing.ContentAlignment.MiddleRight
        '
        'btnSet
        '
        Me.btnSet.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnSet.BackColor = System.Drawing.Color.FromArgb(CType(CType(244, Byte), Integer), CType(CType(202, Byte), Integer), CType(CType(68, Byte), Integer))
        Me.btnSet.FlatAppearance.BorderColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.btnSet.FlatAppearance.BorderSize = 2
        Me.btnSet.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnSet.Font = New System.Drawing.Font("Tahoma", 14.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnSet.ForeColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.btnSet.Location = New System.Drawing.Point(1127, 14)
        Me.btnSet.Name = "btnSet"
        Me.btnSet.Size = New System.Drawing.Size(99, 33)
        Me.btnSet.TabIndex = 7
        Me.btnSet.Text = "Change"
        Me.btnSet.UseVisualStyleBackColor = False
        '
        'lblTitle
        '
        Me.lblTitle.AutoSize = True
        Me.lblTitle.BackColor = System.Drawing.Color.Transparent
        Me.lblTitle.Font = New System.Drawing.Font("Tahoma", 20.25!)
        Me.lblTitle.ForeColor = System.Drawing.Color.FromArgb(CType(CType(244, Byte), Integer), CType(CType(202, Byte), Integer), CType(CType(68, Byte), Integer))
        Me.lblTitle.Location = New System.Drawing.Point(51, 14)
        Me.lblTitle.Name = "lblTitle"
        Me.lblTitle.Size = New System.Drawing.Size(588, 33)
        Me.lblTitle.TabIndex = 4
        Me.lblTitle.Text = "Zamboanga City Medical Center : Payroll System"
        '
        'PictureBox1
        '
        Me.PictureBox1.BackColor = System.Drawing.Color.Transparent
        Me.PictureBox1.BackgroundImage = Global.zcmc_payroll_client_v2.My.Resources.Resources.zcmc_logo
        Me.PictureBox1.BackgroundImageLayout = System.Windows.Forms.ImageLayout.Stretch
        Me.PictureBox1.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Pixel)
        Me.PictureBox1.ForeColor = System.Drawing.Color.FromArgb(CType(CType(222, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer), CType(CType(0, Byte), Integer))
        Me.PictureBox1.Location = New System.Drawing.Point(3, 6)
        Me.PictureBox1.Name = "PictureBox1"
        Me.PictureBox1.Size = New System.Drawing.Size(42, 48)
        Me.PictureBox1.TabIndex = 3
        Me.PictureBox1.TabStop = False
        '
        'Panel1
        '
        Me.Panel1.BackColor = System.Drawing.Color.FromArgb(CType(CType(10, Byte), Integer), CType(CType(62, Byte), Integer), CType(CType(48, Byte), Integer))
        Me.Panel1.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle
        Me.Panel1.Controls.Add(Me.btnMinimize)
        Me.Panel1.Controls.Add(Me.btnMaximize)
        Me.Panel1.Dock = System.Windows.Forms.DockStyle.Top
        Me.Panel1.Location = New System.Drawing.Point(0, 0)
        Me.Panel1.Name = "Panel1"
        Me.Panel1.Size = New System.Drawing.Size(1336, 31)
        Me.Panel1.TabIndex = 0
        '
        'btnMinimize
        '
        Me.btnMinimize.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnMinimize.BackColor = System.Drawing.Color.Transparent
        Me.btnMinimize.BackgroundImage = CType(resources.GetObject("btnMinimize.BackgroundImage"), System.Drawing.Image)
        Me.btnMinimize.BackgroundImageLayout = System.Windows.Forms.ImageLayout.Center
        Me.btnMinimize.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnMinimize.ForeColor = System.Drawing.Color.White
        Me.btnMinimize.Location = New System.Drawing.Point(1251, 3)
        Me.btnMinimize.Name = "btnMinimize"
        Me.btnMinimize.Size = New System.Drawing.Size(37, 22)
        Me.btnMinimize.TabIndex = 3
        Me.btnMinimize.UseVisualStyleBackColor = False
        '
        'btnMaximize
        '
        Me.btnMaximize.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnMaximize.BackColor = System.Drawing.Color.Red
        Me.btnMaximize.BackgroundImage = Global.zcmc_payroll_client_v2.My.Resources.Resources.close_16
        Me.btnMaximize.BackgroundImageLayout = System.Windows.Forms.ImageLayout.Center
        Me.btnMaximize.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnMaximize.ForeColor = System.Drawing.Color.Red
        Me.btnMaximize.Location = New System.Drawing.Point(1294, 3)
        Me.btnMaximize.Name = "btnMaximize"
        Me.btnMaximize.Size = New System.Drawing.Size(37, 22)
        Me.btnMaximize.TabIndex = 2
        Me.btnMaximize.UseVisualStyleBackColor = False
        '
        'FrmStepper
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.White
        Me.ClientSize = New System.Drawing.Size(1338, 750)
        Me.Controls.Add(Me.panelContainer)
        Me.FormBorderStyle = System.Windows.Forms.FormBorderStyle.None
        Me.Name = "FrmStepper"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterParent
        Me.WindowState = System.Windows.Forms.FormWindowState.Maximized
        Me.panelContainer.ResumeLayout(False)
        Me.pnlContent.ResumeLayout(False)
        Me.panelFooter.ResumeLayout(False)
        Me.panelStepper.ResumeLayout(False)
        Me.panelNavbar.ResumeLayout(False)
        Me.panelNavbar.PerformLayout()
        CType(Me.PictureBox1, System.ComponentModel.ISupportInitialize).EndInit()
        Me.Panel1.ResumeLayout(False)
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents panelContainer As Panel
    Friend WithEvents pnlContent As Panel
    Friend WithEvents pnlUserControlDisplay As Panel
    Friend WithEvents panelFooter As Panel
    Friend WithEvents btnNext As Button
    Friend WithEvents btnBack As Button
    Friend WithEvents panelStepper As Panel
    Friend WithEvents tblpStepper As TableLayoutPanel
    Friend WithEvents panelNavbar As Panel
    Friend WithEvents btnMore As Button
    Friend WithEvents lblMonthYear As Label
    Friend WithEvents btnSet As Button
    Friend WithEvents lblTitle As Label
    Friend WithEvents PictureBox1 As PictureBox
    Friend WithEvents Panel1 As Panel
    Friend WithEvents btnMinimize As Button
    Friend WithEvents btnMaximize As Button
    Friend WithEvents pbLoading As ProgressBar
End Class
