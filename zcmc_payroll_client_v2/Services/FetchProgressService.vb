Public Class FetchProgressService
    Public Async Function GetProgress() As Task(Of FetchProgressResponse)
        Try
            Dim response = Await FetchProgressApi.GetProgress()

            If response Is Nothing OrElse response.data Is Nothing Then Exit Function

            Return response.data
        Catch ex As Exception
            Debug.Print(ex.Message)
        End Try
    End Function
End Class
