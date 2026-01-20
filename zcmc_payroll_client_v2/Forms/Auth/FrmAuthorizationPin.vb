Public Class FrmAuthorizationPin
    Dim service As New AuthService

    Private Async Sub btnSubmit_Click(sender As Object, e As EventArgs) Handles btnSubmit.Click
        Dim result = Await service.AuthenticatePin(txtAuthorizationPin.Text)
        If result = True Then
            Me.DialogResult = DialogResult.OK
        End If
    End Sub
End Class