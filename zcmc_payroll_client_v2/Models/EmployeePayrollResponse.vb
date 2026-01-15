Public Class EmployeePayrollResponse
    Public Property id As Integer
    Public Property employee As EmployeeResponse
    Public Property employee_time_record As EmployeeTimeRecordResponse
    Public Property payroll_period As PayrollPeriodResponse
    Public Property month As String
    Public Property year As String
    Public Property gross_salary As String
    Public Property total_deductions As String
    Public Property total_receivables As String
    Public Property net_pay As String
    Public Property employee_deduction As List(Of EmployeeDeductionResponse)
    Public Property employee_receivable As List(Of EmployeeReceivableResponse)
End Class
