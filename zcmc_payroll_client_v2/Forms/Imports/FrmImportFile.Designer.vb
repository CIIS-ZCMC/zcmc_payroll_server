<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class FrmImportFile
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
        Me.TableLayoutPanelContainer = New System.Windows.Forms.TableLayoutPanel()
        Me.Panel1 = New System.Windows.Forms.Panel()
        Me.Label2 = New System.Windows.Forms.Label()
        Me.lblMonth = New System.Windows.Forms.Label()
        Me.lblDeductionType = New System.Windows.Forms.Label()
        Me.panelContent = New System.Windows.Forms.Panel()
        Me.GroupBox2 = New System.Windows.Forms.GroupBox()
        Me.btnBrowser = New System.Windows.Forms.Button()
        Me.txtFile = New System.Windows.Forms.TextBox()
        Me.panelBottom = New System.Windows.Forms.Panel()
        Me.btnCancel = New System.Windows.Forms.Button()
        Me.btnImport = New System.Windows.Forms.Button()
        Me.panelContainer.SuspendLayout()
        Me.TableLayoutPanelContainer.SuspendLayout()
        Me.Panel1.SuspendLayout()
        Me.panelContent.SuspendLayout()
        Me.GroupBox2.SuspendLayout()
        Me.panelBottom.SuspendLayout()
        Me.SuspendLayout()
        '
        'panelContainer
        '
        Me.panelContainer.Controls.Add(Me.TableLayoutPanelContainer)
        Me.panelContainer.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContainer.Location = New System.Drawing.Point(0, 0)
        Me.panelContainer.Name = "panelContainer"
        Me.panelContainer.Size = New System.Drawing.Size(562, 184)
        Me.panelContainer.TabIndex = 1
        '
        'TableLayoutPanelContainer
        '
        Me.TableLayoutPanelContainer.CellBorderStyle = System.Windows.Forms.TableLayoutPanelCellBorderStyle.[Single]
        Me.TableLayoutPanelContainer.ColumnCount = 1
        Me.TableLayoutPanelContainer.ColumnStyles.Add(New System.Windows.Forms.ColumnStyle(System.Windows.Forms.SizeType.Percent, 50.0!))
        Me.TableLayoutPanelContainer.Controls.Add(Me.Panel1, 0, 0)
        Me.TableLayoutPanelContainer.Controls.Add(Me.panelContent, 0, 1)
        Me.TableLayoutPanelContainer.Controls.Add(Me.panelBottom, 0, 2)
        Me.TableLayoutPanelContainer.Dock = System.Windows.Forms.DockStyle.Fill
        Me.TableLayoutPanelContainer.Location = New System.Drawing.Point(0, 0)
        Me.TableLayoutPanelContainer.Name = "TableLayoutPanelContainer"
        Me.TableLayoutPanelContainer.RowCount = 3
        Me.TableLayoutPanelContainer.RowStyles.Add(New System.Windows.Forms.RowStyle(System.Windows.Forms.SizeType.Percent, 36.58537!))
        Me.TableLayoutPanelContainer.RowStyles.Add(New System.Windows.Forms.RowStyle(System.Windows.Forms.SizeType.Percent, 63.41463!))
        Me.TableLayoutPanelContainer.RowStyles.Add(New System.Windows.Forms.RowStyle(System.Windows.Forms.SizeType.Absolute, 58.0!))
        Me.TableLayoutPanelContainer.Size = New System.Drawing.Size(562, 184)
        Me.TableLayoutPanelContainer.TabIndex = 5
        '
        'Panel1
        '
        Me.Panel1.BackColor = System.Drawing.Color.FromArgb(CType(CType(10, Byte), Integer), CType(CType(62, Byte), Integer), CType(CType(48, Byte), Integer))
        Me.Panel1.Controls.Add(Me.Label2)
        Me.Panel1.Controls.Add(Me.lblMonth)
        Me.Panel1.Controls.Add(Me.lblDeductionType)
        Me.Panel1.Dock = System.Windows.Forms.DockStyle.Fill
        Me.Panel1.Location = New System.Drawing.Point(4, 4)
        Me.Panel1.Name = "Panel1"
        Me.Panel1.Size = New System.Drawing.Size(554, 38)
        Me.Panel1.TabIndex = 0
        '
        'Label2
        '
        Me.Label2.AutoSize = True
        Me.Label2.Font = New System.Drawing.Font("Microsoft Sans Serif", 12.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label2.ForeColor = System.Drawing.Color.White
        Me.Label2.Location = New System.Drawing.Point(376, 9)
        Me.Label2.Name = "Label2"
        Me.Label2.Size = New System.Drawing.Size(75, 20)
        Me.Label2.TabIndex = 25
        Me.Label2.Text = "Month Of"
        '
        'lblMonth
        '
        Me.lblMonth.AutoSize = True
        Me.lblMonth.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblMonth.ForeColor = System.Drawing.Color.White
        Me.lblMonth.Location = New System.Drawing.Point(449, 6)
        Me.lblMonth.Name = "lblMonth"
        Me.lblMonth.Size = New System.Drawing.Size(99, 24)
        Me.lblMonth.TabIndex = 26
        Me.lblMonth.Text = "December"
        '
        'lblDeductionType
        '
        Me.lblDeductionType.AutoSize = True
        Me.lblDeductionType.Font = New System.Drawing.Font("Microsoft Sans Serif", 14.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblDeductionType.ForeColor = System.Drawing.Color.White
        Me.lblDeductionType.Location = New System.Drawing.Point(10, 7)
        Me.lblDeductionType.Name = "lblDeductionType"
        Me.lblDeductionType.Size = New System.Drawing.Size(108, 24)
        Me.lblDeductionType.TabIndex = 24
        Me.lblDeductionType.Text = "GSIS Loans"
        '
        'panelContent
        '
        Me.panelContent.Controls.Add(Me.GroupBox2)
        Me.panelContent.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelContent.Location = New System.Drawing.Point(4, 49)
        Me.panelContent.Name = "panelContent"
        Me.panelContent.Size = New System.Drawing.Size(554, 71)
        Me.panelContent.TabIndex = 1
        '
        'GroupBox2
        '
        Me.GroupBox2.Controls.Add(Me.btnBrowser)
        Me.GroupBox2.Controls.Add(Me.txtFile)
        Me.GroupBox2.Font = New System.Drawing.Font("Segoe UI Semibold", 9.75!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.GroupBox2.Location = New System.Drawing.Point(8, 3)
        Me.GroupBox2.Name = "GroupBox2"
        Me.GroupBox2.Size = New System.Drawing.Size(538, 62)
        Me.GroupBox2.TabIndex = 20
        Me.GroupBox2.TabStop = False
        Me.GroupBox2.Text = "Bulk Update"
        '
        'btnBrowser
        '
        Me.btnBrowser.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnBrowser.Location = New System.Drawing.Point(438, 24)
        Me.btnBrowser.Name = "btnBrowser"
        Me.btnBrowser.Size = New System.Drawing.Size(84, 29)
        Me.btnBrowser.TabIndex = 11
        Me.btnBrowser.Text = "Browse..."
        Me.btnBrowser.UseVisualStyleBackColor = True
        '
        'txtFile
        '
        Me.txtFile.Enabled = False
        Me.txtFile.Font = New System.Drawing.Font("Segoe UI Semibold", 12.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.txtFile.Location = New System.Drawing.Point(6, 24)
        Me.txtFile.Name = "txtFile"
        Me.txtFile.Size = New System.Drawing.Size(426, 29)
        Me.txtFile.TabIndex = 10
        Me.txtFile.Text = "C:\Users\Public\Downloads\example.csv"
        '
        'panelBottom
        '
        Me.panelBottom.BackColor = System.Drawing.Color.WhiteSmoke
        Me.panelBottom.Controls.Add(Me.btnCancel)
        Me.panelBottom.Controls.Add(Me.btnImport)
        Me.panelBottom.Dock = System.Windows.Forms.DockStyle.Fill
        Me.panelBottom.Location = New System.Drawing.Point(4, 127)
        Me.panelBottom.Name = "panelBottom"
        Me.panelBottom.Size = New System.Drawing.Size(554, 53)
        Me.panelBottom.TabIndex = 2
        '
        'btnCancel
        '
        Me.btnCancel.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnCancel.BackColor = System.Drawing.Color.White
        Me.btnCancel.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnCancel.Font = New System.Drawing.Font("Segoe UI", 12.0!)
        Me.btnCancel.ForeColor = System.Drawing.Color.Black
        Me.btnCancel.Location = New System.Drawing.Point(137, 12)
        Me.btnCancel.Name = "btnCancel"
        Me.btnCancel.Size = New System.Drawing.Size(132, 33)
        Me.btnCancel.TabIndex = 5
        Me.btnCancel.Text = "Cancel"
        Me.btnCancel.UseVisualStyleBackColor = False
        '
        'btnImport
        '
        Me.btnImport.Anchor = CType((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.btnImport.BackColor = System.Drawing.Color.FromArgb(CType(CType(15, Byte), Integer), CType(CType(87, Byte), Integer), CType(CType(33, Byte), Integer))
        Me.btnImport.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnImport.Font = New System.Drawing.Font("Segoe UI", 12.0!)
        Me.btnImport.ForeColor = System.Drawing.Color.White
        Me.btnImport.Location = New System.Drawing.Point(275, 12)
        Me.btnImport.Name = "btnImport"
        Me.btnImport.Size = New System.Drawing.Size(132, 33)
        Me.btnImport.TabIndex = 4
        Me.btnImport.Text = "Import"
        Me.btnImport.UseVisualStyleBackColor = False
        '
        'FrmImportFile
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.White
        Me.ClientSize = New System.Drawing.Size(562, 184)
        Me.Controls.Add(Me.panelContainer)
        Me.FormBorderStyle = System.Windows.Forms.FormBorderStyle.None
        Me.Name = "FrmImportFile"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen
        Me.panelContainer.ResumeLayout(False)
        Me.TableLayoutPanelContainer.ResumeLayout(False)
        Me.Panel1.ResumeLayout(False)
        Me.Panel1.PerformLayout()
        Me.panelContent.ResumeLayout(False)
        Me.GroupBox2.ResumeLayout(False)
        Me.GroupBox2.PerformLayout()
        Me.panelBottom.ResumeLayout(False)
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents panelContainer As Panel
    Friend WithEvents GroupBox2 As GroupBox
    Friend WithEvents btnBrowser As Button
    Friend WithEvents txtFile As TextBox
    Friend WithEvents Label2 As Label
    Friend WithEvents lblDeductionType As Label
    Friend WithEvents lblMonth As Label
    Friend WithEvents TableLayoutPanelContainer As TableLayoutPanel
    Friend WithEvents Panel1 As Panel
    Friend WithEvents panelContent As Panel
    Friend WithEvents panelBottom As Panel
    Friend WithEvents btnCancel As Button
    Friend WithEvents btnImport As Button
End Class
