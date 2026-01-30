Public Class EmployeePreviewService
    Dim helpers As New Helpers
    Public Async Function GetExcluded(dgv As DataGridView, selected_employees As List(Of Integer),
                                      context As PaginationContext) As Task(Of EmployeePreviewResponse)
        Try
            dgv.Rows.Clear()

            Dim response = Await EmployeePreviewApi.GetPreview(AppState.PayrollPeriodId, "excluded", selected_employees, context.PerPage, context.Page)

            If response Is Nothing OrElse response.data Is Nothing Then Exit Function

            context.Page = response.meta.current_page
            context.LastPage = response.meta.last_page
            context.Total = response.meta.total

            Dim i As Integer = 1
            For Each data As EmployeePreviewResponse In response.data
                dgv.Rows.Add(i, data.id, data.employee_number, data.full_name, data.designation, data.assigned_area.details.name, data.reason, data.status,
                             "View", "Include", "Manage Deduction", "Manage Receivable")
                i += 1
            Next

            dgv.Refresh()

            Return Nothing
        Catch ex As Exception
            Debug.Print(ex.Message)
        End Try
    End Function

    Public Async Function GetIncluded(dgv As DataGridView, lbl As Label,
                                      selected_employees As List(Of Integer),
                                      context As PaginationContext) As Task(Of EmployeePreviewResponse)
        Try
            dgv.Rows.Clear()

            Dim response = Await EmployeePreviewApi.GetPreview(AppState.PayrollPeriodId, "included", selected_employees, context.PerPage, context.Page)

            If response Is Nothing OrElse response.data Is Nothing Then
                lbl.Visible = True
                lbl.Text = "No data is found."
            End If

            context.Page = response.meta.current_page
            context.LastPage = response.meta.last_page
            context.Total = response.meta.total

            Dim i As Integer = 1
            For Each data As EmployeePreviewResponse In response.data
                dgv.Rows.Add(False, i, data.id, data.employee_number, data.full_name, data.designation, data.assigned_area.details.name)
                i += 1
            Next

        Catch ex As Exception
            Debug.Print(ex.Message)
        End Try
    End Function

    Public Async Function GetSelected(dgv As DataGridView, lbl As Label,
                                      selected_employees As List(Of Integer),
                                      context As PaginationContext) As Task(Of EmployeePreviewResponse)
        Try
            dgv.Rows.Clear()

            Dim response = Await EmployeePreviewApi.GetPreview(AppState.PayrollPeriodId, "selected", selected_employees, context.PerPage, context.Page)

            If response Is Nothing OrElse response.data Is Nothing Then
                lbl.Visible = True
                lbl.Text = "No data is found."
            End If

            context.Page = response.meta.current_page
            context.LastPage = response.meta.last_page
            context.Total = response.meta.total

            Dim i As Integer = 1
            For Each data As EmployeePreviewResponse In response.data
                Dim monthName = helpers.GetMonthName(AppState.PayrollPeriod.month)
                dgv.Rows.Add(i, data.id, data.employee_number, data.full_name, monthName, AppState.PayrollPeriod.year,
                             data.payroll.basic_pay, data.payroll.total_receivables, data.payroll.gross_pay, data.payroll.total_deductions,
                             data.payroll.net_pay)
                i += 1
            Next

        Catch ex As Exception
            Debug.Print(ex.Message)
        End Try
    End Function
End Class
