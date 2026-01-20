Public Class AuthService

    ' Login logic
    Public Async Function Login(employeeID As String, password As String) As Task(Of LoginResponse)
        Dim response = Await AuthApi.Login(employeeID, password)

        If response.data IsNot Nothing Then
            ' Store token globally for ApiClient
            ApiClient.Token = response.data.token
            Return response.data
        End If

        Return Nothing
    End Function

    ' Logout logic
    Public Async Function Logout() As Task(Of Boolean)
        Dim response = Await AuthApi.Logout()
        If response.message = "success" Then
            ApiClient.Token = ""
            Return True
        End If
        Return False
    End Function

    Public Async Function AuthenticatePin(pin As String) As Task(Of Boolean)
        Dim response = Await AuthApi.AuthenticatePin(pin)
        If response.success = True Then
            Return True
        End If

        Return False
    End Function
End Class
