Public Class EmployeeDeductionDto
    Public Property EmployeeDeductionId As Integer?
    Public Property PayrollPeriodId As Integer
    Public Property EmployeeId As Integer
    Public Property DeductionId As Integer
    Public Property BillingCycle As String ''This is a billing_cycle
    Public Property WithTerms As Boolean
    Public Property TotalTerm As Integer?
    Public Property Amount As Decimal?
    Public Property Percentage As Decimal?
    Public Property Reason As String
    Public Property IsDefault As Boolean
End Class
