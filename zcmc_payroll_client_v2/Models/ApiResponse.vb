Public Class ApiResponse(Of T)
    Public Property data As T
    Public Property meta As MetaResponse
    Public Property message As String
    Public Property success As Boolean
End Class

Public Class MetaResponse
    Public Property current_page As Integer
    Public Property last_page As Integer
    Public Property per_page As Integer
    Public Property total As Integer
    Public Property item_from As Integer
    Public Property item_to As Integer
End Class
