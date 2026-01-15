Public Class ServiceResult
    Public Property Success As Boolean
    Public Property Message As String
    Public Property ErrorCode As ServiceErrorCode
    Public Property Timestamp As DateTime = DateTime.Now

    Public Shared Function Ok(message As String) As ServiceResult
        Return New ServiceResult With {
            .Success = True,
            .Message = message
        }
    End Function

    Public Shared Function Fail(message As String) As ServiceResult
        Return New ServiceResult With {
            .Success = False,
            .Message = message
        }
    End Function
End Class
