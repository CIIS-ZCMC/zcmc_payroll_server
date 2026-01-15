Public Class EmployeeDeductionService
    Public Delegate Sub DataCallback(message As String, success As Boolean, timestamp As DateTime)

    Public Async Function CreateSingle(formData As EmployeeDeductionDto) As Task(Of ServiceResult)
        Try
            Dim data As New Dictionary(Of String, Object)
            data.Add("payroll_period_id", AppState.PayrollPeriodId)
            data.Add("employee_id", formData.EmployeeId)
            data.Add("deduction_id", formData.DeductionId)
            data.Add("amount", formData.Amount)
            data.Add("percentage", formData.Percentage)
            data.Add("frequency", formData.BillingCycle)
            data.Add("date_from", Nothing)
            data.Add("date_to", Nothing)
            data.Add("with_terms", formData.WithTerms)
            data.Add("total_term", formData.TotalTerm)
            data.Add("reason", formData.Reason)
            data.Add("is_default", formData.IsDefault)

            Dim response = Await EmployeeDeductionApi.Create(data)

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

    Public Async Function CreateBulk(formData As List(Of Dictionary(Of String, Object))) As Task(Of ServiceResult)
        Try
            Dim data As New Dictionary(Of String, Object)
            data.Add("payroll_period_id", AppState.PayrollPeriodId)
            data.Add("deductions", formData)

            Dim response = Await EmployeeDeductionApi.Create(data)

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
