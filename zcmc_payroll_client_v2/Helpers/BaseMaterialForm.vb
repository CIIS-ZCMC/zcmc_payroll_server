Imports MaterialSkin
Imports MaterialSkin.Controls
Public Class BaseMaterialForm
    Inherits MaterialForm

    Protected Overrides Sub OnLoad(e As EventArgs)
        MyBase.OnLoad(e)

        Dim skinManager = MaterialSkinManager.Instance
        skinManager.AddFormToManage(Me)

        skinManager.Theme = MaterialSkinManager.Themes.LIGHT
        skinManager.ColorScheme = New ColorScheme(
            Primary.Blue800,
            Primary.Blue700,
            Primary.Blue200,
            Accent.LightBlue200,
            TextShade.WHITE
        )
    End Sub

    Private Sub InitializeComponent()
        Me.SuspendLayout()
        '
        'BaseMaterialForm
        '
        Me.ClientSize = New System.Drawing.Size(991, 543)
        Me.Name = "BaseMaterialForm"
        Me.ResumeLayout(False)

    End Sub
End Class
