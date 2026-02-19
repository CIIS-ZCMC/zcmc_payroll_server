Public Class PayrollProcessService
    Public Async Function GetOrCreateProcess(payrollPeriodId As Integer, payrollType As Integer) As Task(Of PayrollProcessResponse)
        Dim find = Await PayrollProcessApi.Show(payrollPeriodId, payrollType)

        If find IsNot Nothing AndAlso find.data IsNot Nothing Then
            Return find.data
        End If

        Dim dto As New PayrollProcessDto With {
            .payroll_period_id = payrollPeriodId,
            .payroll_type = payrollType,
            .current_step = 1,
            .status = "in_progress",
            .started_by = "Test"
        }

        Dim create = Await PayrollProcessApi.Create(dto)
        Return create.data
    End Function

    Public Async Function UpdateProcess(id As Integer, stepNumber As Integer, status As String) As Task(Of ServiceResult)
        Try
            Dim payload As New Dictionary(Of String, Object)
            payload.Add("current_step", stepNumber)
            payload.Add("status", status)

            Dim response = Await PayrollProcessApi.Update(id, payload)

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