Public Class EmployeePayrollResponse
    Public Property id As Integer
    Public Property employee As EmployeeResponse
    Public Property employee_time_record As EmployeeTimeRecordResponse
    Public Property payroll_period As PayrollPeriodResponse
    Public Property month As Integer
    Public Property year As Integer
    Public Property basic_pay As Decimal
    Public Property total_receivables As Decimal
    Public Property gross_pay As Decimal
    Public Property total_deductions As Decimal
    Public Property net_pay As Decimal
End Class
