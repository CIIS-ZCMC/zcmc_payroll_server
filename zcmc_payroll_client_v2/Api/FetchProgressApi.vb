Public Class FetchProgressApi
    Public Shared Async Function GetProgress() As Task(Of ApiResponse(Of FetchProgressResponse))
        Return Await ApiClient.GetAsync(Of FetchProgressResponse)(urlFetchProgress)
    End Function
End Class
