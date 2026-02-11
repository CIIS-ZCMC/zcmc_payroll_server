Public Class EmployeeReceivableService
    Public Async Function CreateSingle(formData As EmployeeReceivableDto) As Task(Of ServiceResult)
        Try
            Dim data As New Dictionary(Of String, Object)
            data.Add("payroll_period_id", AppState.PayrollPeriodId)
            data.Add("employee_id", formData.EmployeeId)
            data.Add("receivable_id", formData.ReceivableId)
            data.Add("billing_cycle", formData.BillingCycle)
            data.Add("amount", formData.Amount)
            data.Add("percentage", formData.Percentage)
            data.Add("reason", formData.Reason)
            data.Add("is_default", formData.IsDefault)

            Dim response = Await EmployeeReceivableApi.Create(data)

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
            data.Add("receivables", formData)

            Dim response = Await EmployeeReceivableApi.Create(data)

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

    Public Async Function Update(id As Integer, formData As EmployeeReceivableDto, mode As String) As Task(Of ServiceResult)
        Try
            Dim payload As New Dictionary(Of String, Object)
            payload.Add("mode", mode)
            payload.Add("payroll_period_id", AppState.PayrollPeriodId)
            payload.Add("employee_id", formData.EmployeeId)
            payload.Add("receivable_id", formData.ReceivableId)
            payload.Add("billing_cycle", formData.BillingCycle)
            payload.Add("amount", formData.Amount)
            payload.Add("percentage", formData.Percentage)
            payload.Add("reason", formData.Reason)
            payload.Add("is_default", formData.IsDefault)

            Dim response = Await EmployeeReceivableApi.Update(id, payload)

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
