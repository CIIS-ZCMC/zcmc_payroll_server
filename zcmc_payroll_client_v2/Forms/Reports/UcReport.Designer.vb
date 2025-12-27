<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class UcReport
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
        Me.panelContainer = New System.Windows.Forms.Panel()
        Me.panelContent = New System.Windows.Forms.Panel()
        Me.GroupBox1 = New System.Windows.Forms.GroupBox()
        Me.btnExport = New System.Windows.Forms.Button()
        Me.cmbYear = New System.Windows.Forms.ComboBox()
        Me.cmbMonth = New System.Windows.Forms.ComboBox()
        Me.Label3 = New System.Windows.Forms.Label()
        Me.Label4 = New System.Windows.Forms.Label()
        Me.rdbSecondHalf = New System.Windows.Forms.RadioButton()
        Me.rdbFirstHalf = New System.Windows.Forms.RadioButton()
        Me.cmbEmploymentType = New System.Windows.Forms.ComboBox()
        Me.cmbTypeOfReport = New System.Windows.Forms.ComboBox()
        Me.Label1 = New System.Windows.Forms.Label()
        Me.Label2 = New System.Windows.Forms.Label()
        Me.btnGenerateReport = New System.Windows.Forms.Button()
        Me.panelTop = New System.Windows.Forms.Panel()
        Me.lblDescription = New System.Windows.Forms.Label()
        Me.lblTitle = New System.Windows.Forms.Label()
        Me.panelContainer.SuspendLayout()
        Me.panelContent.SuspendLayout()
        Me.GroupBox1.SuspendLayout()
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
        Me.panelContainer.Size = New System.Drawing.Size(957, 528)
        Me.panelContainer.TabIndex = 2
        '
        'panelContent
        '
        Me.panelContent.Controls.Add(Me.GroupBox1)
        Me.panelContent.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContent.Location = New System.Drawing.Point(0, 66)
        Me.panelContent.Name = "panelContent"
        Me.panelContent.Size = New System.Drawing.Size(957, 462)
        Me.panelContent.TabIndex = 33
        '
        'GroupBox1
        '
        Me.GroupBox1.Anchor = System.Windows.Forms.AnchorStyles.Top
        Me.GroupBox1.Controls.Add(Me.btnExport)
        Me.GroupBox1.Controls.Add(Me.cmbYear)
        Me.GroupBox1.Controls.Add(Me.cmbMonth)
        Me.GroupBox1.Controls.Add(Me.Label3)
        Me.GroupBox1.Controls.Add(Me.Label4)
        Me.GroupBox1.Controls.Add(Me.rdbSecondHalf)
        Me.GroupBox1.Controls.Add(Me.rdbFirstHalf)
        Me.GroupBox1.Controls.Add(Me.cmbEmploymentType)
        Me.GroupBox1.Controls.Add(Me.cmbTypeOfReport)
        Me.GroupBox1.Controls.Add(Me.Label1)
        Me.GroupBox1.Controls.Add(Me.Label2)
        Me.GroupBox1.Controls.Add(Me.btnGenerateReport)
        Me.GroupBox1.Location = New System.Drawing.Point(139, 47)
        Me.GroupBox1.Name = "GroupBox1"
        Me.GroupBox1.Size = New System.Drawing.Size(679, 280)
        Me.GroupBox1.TabIndex = 0
        Me.GroupBox1.TabStop = False
        '
        'btnExport
        '
        Me.btnExport.BackColor = System.Drawing.Color.White
        Me.btnExport.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnExport.Font = New System.Drawing.Font("Segoe UI", 15.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnExport.ForeColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.btnExport.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnExport.Location = New System.Drawing.Point(339, 221)
        Me.btnExport.Margin = New System.Windows.Forms.Padding(0)
        Me.btnExport.Name = "btnExport"
        Me.btnExport.Size = New System.Drawing.Size(168, 40)
        Me.btnExport.TabIndex = 39
        Me.btnExport.Text = "Export"
        Me.btnExport.UseVisualStyleBackColor = False
        '
        'cmbYear
        '
        Me.cmbYear.AutoCompleteMode = System.Windows.Forms.AutoCompleteMode.SuggestAppend
        Me.cmbYear.DrawMode = System.Windows.Forms.DrawMode.OwnerDrawFixed
        Me.cmbYear.FlatStyle = System.Windows.Forms.FlatStyle.System
        Me.cmbYear.Font = New System.Drawing.Font("Segoe UI", 15.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.cmbYear.FormattingEnabled = True
        Me.cmbYear.Location = New System.Drawing.Point(587, 64)
        Me.cmbYear.Name = "cmbYear"
        Me.cmbYear.Size = New System.Drawing.Size(86, 36)
        Me.cmbYear.Sorted = True
        Me.cmbYear.TabIndex = 38
        '
        'cmbMonth
        '
        Me.cmbMonth.AutoCompleteMode = System.Windows.Forms.AutoCompleteMode.SuggestAppend
        Me.cmbMonth.DrawMode = System.Windows.Forms.DrawMode.OwnerDrawFixed
        Me.cmbMonth.FlatStyle = System.Windows.Forms.FlatStyle.System
        Me.cmbMonth.Font = New System.Drawing.Font("Segoe UI", 15.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.cmbMonth.FormattingEnabled = True
        Me.cmbMonth.Location = New System.Drawing.Point(394, 64)
        Me.cmbMonth.Name = "cmbMonth"
        Me.cmbMonth.Size = New System.Drawing.Size(187, 36)
        Me.cmbMonth.Sorted = True
        Me.cmbMonth.TabIndex = 37
        '
        'Label3
        '
        Me.Label3.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.Label3.AutoSize = True
        Me.Label3.Font = New System.Drawing.Font("Segoe UI", 15.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label3.Location = New System.Drawing.Point(26, 68)
        Me.Label3.Name = "Label3"
        Me.Label3.Size = New System.Drawing.Size(326, 30)
        Me.Label3.TabIndex = 36
        Me.Label3.Text = "Select Salary Period (Month/ Year)"
        '
        'Label4
        '
        Me.Label4.AutoSize = True
        Me.Label4.Font = New System.Drawing.Font("Segoe UI", 15.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label4.Location = New System.Drawing.Point(26, 154)
        Me.Label4.Name = "Label4"
        Me.Label4.Size = New System.Drawing.Size(132, 30)
        Me.Label4.TabIndex = 35
        Me.Label4.Text = "Salary Period"
        '
        'rdbSecondHalf
        '
        Me.rdbSecondHalf.AutoSize = True
        Me.rdbSecondHalf.Font = New System.Drawing.Font("Segoe UI", 15.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.rdbSecondHalf.Location = New System.Drawing.Point(513, 152)
        Me.rdbSecondHalf.Name = "rdbSecondHalf"
        Me.rdbSecondHalf.Size = New System.Drawing.Size(145, 34)
        Me.rdbSecondHalf.TabIndex = 34
        Me.rdbSecondHalf.TabStop = True
        Me.rdbSecondHalf.Text = "Second-Half"
        Me.rdbSecondHalf.UseVisualStyleBackColor = True
        '
        'rdbFirstHalf
        '
        Me.rdbFirstHalf.AutoSize = True
        Me.rdbFirstHalf.Font = New System.Drawing.Font("Segoe UI", 15.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.rdbFirstHalf.Location = New System.Drawing.Point(394, 152)
        Me.rdbFirstHalf.Name = "rdbFirstHalf"
        Me.rdbFirstHalf.Size = New System.Drawing.Size(113, 34)
        Me.rdbFirstHalf.TabIndex = 33
        Me.rdbFirstHalf.TabStop = True
        Me.rdbFirstHalf.Text = "First Half"
        Me.rdbFirstHalf.UseVisualStyleBackColor = True
        '
        'cmbEmploymentType
        '
        Me.cmbEmploymentType.AutoCompleteMode = System.Windows.Forms.AutoCompleteMode.SuggestAppend
        Me.cmbEmploymentType.DrawMode = System.Windows.Forms.DrawMode.OwnerDrawFixed
        Me.cmbEmploymentType.FlatStyle = System.Windows.Forms.FlatStyle.System
        Me.cmbEmploymentType.Font = New System.Drawing.Font("Segoe UI", 15.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.cmbEmploymentType.FormattingEnabled = True
        Me.cmbEmploymentType.Items.AddRange(New Object() {"Job Order", "Permanent"})
        Me.cmbEmploymentType.Location = New System.Drawing.Point(394, 108)
        Me.cmbEmploymentType.Name = "cmbEmploymentType"
        Me.cmbEmploymentType.Size = New System.Drawing.Size(279, 36)
        Me.cmbEmploymentType.Sorted = True
        Me.cmbEmploymentType.TabIndex = 31
        '
        'cmbTypeOfReport
        '
        Me.cmbTypeOfReport.AutoCompleteMode = System.Windows.Forms.AutoCompleteMode.SuggestAppend
        Me.cmbTypeOfReport.DrawMode = System.Windows.Forms.DrawMode.OwnerDrawFixed
        Me.cmbTypeOfReport.FlatStyle = System.Windows.Forms.FlatStyle.System
        Me.cmbTypeOfReport.Font = New System.Drawing.Font("Segoe UI", 15.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.cmbTypeOfReport.FormattingEnabled = True
        Me.cmbTypeOfReport.Items.AddRange(New Object() {"Salary Payroll", "Salary Payroll Summary Sheet"})
        Me.cmbTypeOfReport.Location = New System.Drawing.Point(394, 19)
        Me.cmbTypeOfReport.Name = "cmbTypeOfReport"
        Me.cmbTypeOfReport.Size = New System.Drawing.Size(279, 36)
        Me.cmbTypeOfReport.Sorted = True
        Me.cmbTypeOfReport.TabIndex = 32
        '
        'Label1
        '
        Me.Label1.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.Label1.AutoSize = True
        Me.Label1.Font = New System.Drawing.Font("Segoe UI", 15.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label1.Location = New System.Drawing.Point(26, 23)
        Me.Label1.Name = "Label1"
        Me.Label1.Size = New System.Drawing.Size(148, 30)
        Me.Label1.TabIndex = 28
        Me.Label1.Text = "Type of Report"
        '
        'Label2
        '
        Me.Label2.Anchor = CType(((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.Label2.AutoSize = True
        Me.Label2.Font = New System.Drawing.Font("Segoe UI", 15.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label2.Location = New System.Drawing.Point(26, 112)
        Me.Label2.Name = "Label2"
        Me.Label2.Size = New System.Drawing.Size(175, 30)
        Me.Label2.TabIndex = 29
        Me.Label2.Text = "Employment type"
        '
        'btnGenerateReport
        '
        Me.btnGenerateReport.BackColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.btnGenerateReport.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnGenerateReport.Font = New System.Drawing.Font("Segoe UI", 15.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnGenerateReport.ForeColor = System.Drawing.Color.White
        Me.btnGenerateReport.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnGenerateReport.Location = New System.Drawing.Point(161, 221)
        Me.btnGenerateReport.Margin = New System.Windows.Forms.Padding(0)
        Me.btnGenerateReport.Name = "btnGenerateReport"
        Me.btnGenerateReport.Size = New System.Drawing.Size(168, 40)
        Me.btnGenerateReport.TabIndex = 30
        Me.btnGenerateReport.Text = "Generate Report"
        Me.btnGenerateReport.UseVisualStyleBackColor = False
        '
        'panelTop
        '
        Me.panelTop.BackColor = System.Drawing.Color.FromArgb(CType(CType(10, Byte), Integer), CType(CType(62, Byte), Integer), CType(CType(48, Byte), Integer))
        Me.panelTop.Controls.Add(Me.lblDescription)
        Me.panelTop.Controls.Add(Me.lblTitle)
        Me.panelTop.Dock = System.Windows.Forms.DockStyle.Top
        Me.panelTop.Location = New System.Drawing.Point(0, 0)
        Me.panelTop.Name = "panelTop"
        Me.panelTop.Size = New System.Drawing.Size(957, 66)
        Me.panelTop.TabIndex = 32
        '
        'lblDescription
        '
        Me.lblDescription.AutoSize = True
        Me.lblDescription.Font = New System.Drawing.Font("Segoe UI", 10.0!)
        Me.lblDescription.ForeColor = System.Drawing.Color.White
        Me.lblDescription.Location = New System.Drawing.Point(16, 32)
        Me.lblDescription.Name = "lblDescription"
        Me.lblDescription.Size = New System.Drawing.Size(567, 19)
        Me.lblDescription.TabIndex = 3
        Me.lblDescription.Text = "Generate different types of reports here to suit your-data-intensive workflow req" &
    "uirements."
        '
        'lblTitle
        '
        Me.lblTitle.AutoSize = True
        Me.lblTitle.Font = New System.Drawing.Font("Segoe UI", 12.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblTitle.ForeColor = System.Drawing.Color.White
        Me.lblTitle.Location = New System.Drawing.Point(16, 12)
        Me.lblTitle.Name = "lblTitle"
        Me.lblTitle.Size = New System.Drawing.Size(199, 21)
        Me.lblTitle.TabIndex = 2
        Me.lblTitle.Text = "Generate Payroll Reports"
        Me.lblTitle.TextAlign = System.Drawing.ContentAlignment.MiddleCenter
        '
        'UcReport
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.White
        Me.Controls.Add(Me.panelContainer)
        Me.Name = "UcReport"
        Me.Size = New System.Drawing.Size(957, 528)
        Me.panelContainer.ResumeLayout(False)
        Me.panelContent.ResumeLayout(False)
        Me.GroupBox1.ResumeLayout(False)
        Me.GroupBox1.PerformLayout()
        Me.panelTop.ResumeLayout(False)
        Me.panelTop.PerformLayout()
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents panelContainer As Panel
    Friend WithEvents panelContent As Panel
    Friend WithEvents panelTop As Panel
    Friend WithEvents lblDescription As Label
    Friend WithEvents lblTitle As Label
    Friend WithEvents GroupBox1 As GroupBox
    Friend WithEvents btnExport As Button
    Friend WithEvents cmbYear As ComboBox
    Friend WithEvents cmbMonth As ComboBox
    Friend WithEvents Label3 As Label
    Friend WithEvents Label4 As Label
    Friend WithEvents rdbSecondHalf As RadioButton
    Friend WithEvents rdbFirstHalf As RadioButton
    Friend WithEvents cmbEmploymentType As ComboBox
    Friend WithEvents cmbTypeOfReport As ComboBox
    Friend WithEvents Label1 As Label
    Friend WithEvents Label2 As Label
    Friend WithEvents btnGenerateReport As Button
End Class
