Imports System.Net.Http
Imports System.Text
Imports Newtonsoft.Json


Public Class ApiClient
    Public Shared ReadOnly client As New HttpClient()
    Public Shared Token As String = ""

    Private Shared Sub ApplyHeaders()
        client.DefaultRequestHeaders.Clear()
        client.DefaultRequestHeaders.Add("Accept", "application/json")

        If Token <> "" Then
            client.DefaultRequestHeaders.Authorization =
                New Headers.AuthenticationHeaderValue("Bearer", Token)
        End If
    End Sub

    Public Shared Async Function GetAsync(Of T)(endpoint As String) As Task(Of APIresponse(Of T))
        ApplyHeaders()
        Dim response = Await client.GetAsync(BaseUrl & endpoint)
        Dim json = Await response.Content.ReadAsStringAsync()
        Return JsonConvert.DeserializeObject(Of APIresponse(Of T))(json)
    End Function

    Public Shared Async Function PostAsync(Of T)(endpoint As String, data As Object) As Task(Of APIresponse(Of T))
        ApplyHeaders()
        Dim content = New StringContent(JsonConvert.SerializeObject(data), Encoding.UTF8, "application/json")
        Dim response = Await client.PostAsync(BaseUrl & endpoint, content)
        Dim json = Await response.Content.ReadAsStringAsync()
        Return JsonConvert.DeserializeObject(Of APIresponse(Of T))(json)
    End Function

    Public Shared Async Function PutAsync(Of T)(endpoint As String, data As Object) As Task(Of APIresponse(Of T))
        ApplyHeaders()
        Dim content = New StringContent(JsonConvert.SerializeObject(data), Encoding.UTF8, "application/json")
        Dim response = Await client.PutAsync(BaseUrl & endpoint, content)
        Dim json = Await response.Content.ReadAsStringAsync()
        Return JsonConvert.DeserializeObject(Of APIresponse(Of T))(json)
    End Function

    Public Shared Async Function DeleteAsync(Of T)(endpoint As String) As Task(Of APIresponse(Of T))
        ApplyHeaders()
        Dim response = Await client.DeleteAsync(BaseUrl & endpoint)
        Dim json = Await response.Content.ReadAsStringAsync()
        Return JsonConvert.DeserializeObject(Of APIresponse(Of T))(json)
    End Function
End Class
