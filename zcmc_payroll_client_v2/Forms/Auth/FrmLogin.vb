Public Class FrmLogin
    Private authService As New AuthService()

    Private Async Sub btnSignIn_Click(sender As Object, e As EventArgs) Handles btnSignIn.Click
        Dim employeeID = txtEmployeeID.Text.Trim()
        Dim password = txtPassword.Text.Trim()

        If String.IsNullOrEmpty(employeeID) OrElse String.IsNullOrEmpty(password) Then
            MessageBox.Show("Email and Password are required.")
            Return
        End If

        Dim loginResult = Await authService.Login(employeeID, password)

        If loginResult IsNot Nothing Then
            ' Successful login
            'FrmMain.Show()
            FrmStepper.Show()
            'Form1.Show()
            Me.Hide()
        Else
            MessageBox.Show("Login failed. Please check your credentials.")
        End If
    End Sub

    Private Sub btnMinimize_Click(sender As Object, e As EventArgs) Handles btnMinimize.Click

    End Sub

    Private Sub btnClose_Click(sender As Object, e As EventArgs) Handles btnClose.Click
        Me.Close()
    End Sub
End Class