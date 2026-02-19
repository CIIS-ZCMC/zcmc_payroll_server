Public Class FetchEmployeeService
    Public Async Function Fetch(context As PayrollPeriodContext) As Task(Of ServiceResult)
        Try
            Dim response = Await FetchEmployeeApi.FetchOrCreate(context)

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

    Public Async Function Create(context As PayrollPeriodContext) As Task(Of ServiceResult)
        Try
            Dim data As New Dictionary(Of String, Object)
            data.Add("year", context.year)
            data.Add("month", context.month)
            data.Add("employment_type", context.employment_type)
            data.Add("period_type", context.period_type)

            Dim response = Await FetchEmployeeApi.Create(data)

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
