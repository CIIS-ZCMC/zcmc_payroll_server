Imports Newtonsoft.Json
Imports System.Text
Imports System.Net.Http

Public Class AuthApi

    ' Login method
    Public Shared Async Function Login(employeeID As String, password As String) As Task(Of APIresponse(Of LoginResponse))
        Dim payload = New With {
            .employee_id = employeeID,
            .password = password
        }

        Return Await ApiClient.PostAsync(Of LoginResponse)(URLSignin, payload)
    End Function

    ' Logout method
    Public Shared Async Function Logout() As Task(Of APIresponse(Of LoginResponse))
        Return Await ApiClient.PostAsync(Of LoginResponse)(URLSignin, Nothing)
    End Function

End Class
