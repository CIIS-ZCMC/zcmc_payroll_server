Public Class EmployeePayrollService
    Public Async Function Create(payrollType As String, formData As List(Of EmployeePayrollDto)) As Task(Of ServiceResult)
        Try
            Dim data As New Dictionary(Of String, Object)
            data.Add("payroll_type", payrollType)
            data.Add("employee_payroll", formData)

            Dim response = Await EmployeePayrollApi.Create(data)

            If response Is Nothing Then
                Return ServiceResult.Fail("No response from server.")
            End If

            If response.success Then
                Return ServiceResult.Ok(response.message)
            End If

            Return ServiceResult.Fail(response.message)

        Catch ex As Exception
            Return ServiceResult.Fail(ex.Message)
        End Try
    End Function
End Class
