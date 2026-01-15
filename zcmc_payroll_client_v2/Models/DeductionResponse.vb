Public Class DeductionResponse
    Public Property id As Integer
    Public Property uuid As String
    Public Property name As String
    Public Property code As String
    Public Property type As String
    Public Property billing_cycle As String
    Public Property percent_value As Decimal
    Public Property fixed_amount As Decimal
    Public Property date_start As String
    Public Property date_end As String
    Public Property status As String
    Public Property deduction_group_id As Integer
    Public Property deduction_group As DeductionGroupResponse
    Public Property deduction_rule As List(Of DeductionRuleResponse)
End Class
