Public Class EmployeeDeductionResponse
    Public Property id As Integer
    Public Property employee_id As Integer
    Public Property employee As EmployeeResponse
    Public Property payroll_period_id As Integer
    Public Property payroll_period As PayrollPeriodResponse
    Public Property deduction_id As Integer
    Public Property deduction As DeductionResponse
    Public Property billing_cycle As String
    Public Property amount As Decimal
    Public Property percentage As Decimal
    Public Property date_from As String
    Public Property date_to As String
    Public Property with_terms As Integer
    Public Property total_term As Integer
    Public Property total_paid As Integer
    Public Property reason As String
    Public Property status As String
    Public Property isDifferential As String
    Public Property is_default As Boolean
    Public Property effective_date As Date
    Public Property deduct_at As String
    Public Property stopped_at As String
    Public Property completed_at As String
    Public Property created_at As String
    Public Property updated_at As String
    Public Property deleted_at As String

    ' Property for FilePath
    Private _filePath As String
    Public Property FilePath As String
        Get
            Return _filePath
        End Get
        Set(value As String)
            _filePath = value
        End Set
    End Property
End Class
