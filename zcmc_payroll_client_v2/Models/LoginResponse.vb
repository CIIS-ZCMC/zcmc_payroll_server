Imports Newtonsoft.Json.Linq

Public Class LoginData
    Public Property access_token As String
    Public Property name As String
    Public Property email As String
    Public Property designation As String
    Public Property permissions As List(Of String)
End Class

Public Class LoginResponse
    Public Property data As LoginData
    Public Property token As String
    Public Property statusCode As Integer
    Public Property message As String
End Class
