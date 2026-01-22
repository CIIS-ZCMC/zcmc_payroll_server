Public Class EmployeeReceivableDto
    Public Property EmployeeReceivableId As Integer?
    Public Property PayrollPeriodId As Integer
    Public Property EmployeeId As Integer
    Public Property ReceivableId As Integer
    Public Property Frequency As String ''This is a billing_cycle
    Public Property Amount As Decimal?
    Public Property Percentage As Decimal?
    Public Property Reason As String
    Public Property IsDefault As Boolean
End Class
