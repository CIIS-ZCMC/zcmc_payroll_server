Public Enum ServiceErrorCode
    None = 0
    ValidationError = 422
    Unauthorized = 401
    Forbidden = 403
    NotFound = 404
    ApiError = 500
    NetworkError = 900
    Unknown = 999
End Enum