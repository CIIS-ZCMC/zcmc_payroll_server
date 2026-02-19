Imports System.Runtime.Remoting.Contexts

Public Class EmployeeService
    Public Async Function GetEmployee(dgv As DataGridView, type As String, paginate As Boolean, context As PaginationContext) As Task(Of EmployeeResponse)
        Try
            dgv.Rows.Clear()

            Dim response = Await EmployeeApi.GetAll(type, context.PerPage, context.Page)

            If response Is Nothing OrElse response.data Is Nothing Then Exit Function

            context.Page = response.meta.current_page
            context.LastPage = response.meta.last_page
            context.Total = response.meta.total

            Dim i As Integer = 1
            For Each data As EmployeeResponse In response.data
                Dim reason As String = If(data.excluded?.reason, "")
                Dim btnIncludeExclude As String = If(type = "isIncluded", "Exclude", "Include")

                dgv.Rows.Add(i, data.id, data.employee_number, data.full_name, data.designation, data.assigned_area.details.name, reason, data.employee_time_records.status,
                             "View", btnIncludeExclude, "Manage Deduction", "Manage Receivable")
                i += 1
            Next

            dgv.Refresh()

            Return Nothing
        Catch ex As Exception
            Debug.Print(ex.Message)
        End Try
    End Function

    Public Async Function GetEmployeeDeductionList(dgv As DataGridView, id As Integer) As Task(Of EmployeeResponse)
        Try
            dgv.Rows.Clear()
            Dim response = Await EmployeeApi.Show(id)

            If response Is Nothing OrElse response.data Is Nothing Then Exit Function

            Dim data As EmployeeResponse = response.data

            Dim i As Integer = 1
            For Each item In data.deductions
                Dim value As String = If(item?.amount, item.percentage)
                dgv.Rows.Add(i, item.id, item.deduction.code, value)
                i += 1
            Next

            dgv.Refresh()
            Return Nothing
        Catch ex As Exception
            Debug.Print(ex.Message)
        End Try
    End Function

    Public Async Function GetEmployeeReceivableList(dgv As DataGridView, id As Integer) As Task(Of EmployeeResponse)
        Try
            dgv.Rows.Clear()
            Dim response = Await EmployeeApi.Show(id)

            If response Is Nothing OrElse response.data Is Nothing Then Exit Function

            Dim data As EmployeeResponse = response.data

            Dim i As Integer = 1
            For Each item In data.receivables
                Dim value As String = If(item?.amount, item.percentage)
                dgv.Rows.Add(i, item.id, item.receivable.code, value)
                i += 1
            Next

            dgv.Refresh()
            Return Nothing
        Catch ex As Exception
            Debug.Print(ex.Message)
        End Try
    End Function

    Public Async Function ShowEmployeeDeduction(dgv As DataGridView, id As Integer) As Task(Of EmployeeResponse)
        Try
            dgv.Rows.Clear()
            Dim response = Await EmployeeApi.Show(id)

            'If response Is Nothing OrElse response.data Is Nothing Then Exit Function

            Dim data As EmployeeResponse = response.data

            Dim i As Integer = 1
            For Each item In data.deductions
                Dim value As String = If(item?.amount, item.percentage)
                Dim rowIndex As Integer = dgv.Rows.Add(i, item.id, item.payroll_period_id, item.employee_id, item.deduction_id, item.deduction.name, item.deduction.code,
                             item.amount, item.percentage, item.completed_at, item.with_terms, item.total_term, item.total_paid, item.billing_cycle,
                             item.reason, item.is_default, item.status, "Edit", "Stop")
                i += 1
            Next

            dgv.Refresh()
            Return Nothing
        Catch ex As Exception
            Debug.Print(ex.Message)
        End Try
    End Function

    Public Async Function ShowEmployeeReceivable(dgv As DataGridView, id As Integer) As Task(Of EmployeeResponse)
        Try
            dgv.Rows.Clear()
            Dim response = Await EmployeeApi.Show(id)

            If response Is Nothing OrElse response.data Is Nothing Then Exit Function

            Dim data As EmployeeResponse = response.data

            Dim i As Integer = 1
            For Each item In data.receivables
                Dim value As String = If(item?.amount, item.percentage)
                Dim rowIndex As Integer = dgv.Rows.Add(i, item.id, item.payroll_period_id, item.employee_id, item.receivable_id, item.receivable.name, item.receivable.code,
                             item.amount, item.completed_at, item.total_paid, item.billing_cycle, item.reason, item.is_default, item.status, "Edit", "Stop")
                i += 1
            Next

            dgv.Refresh()
            Return Nothing
        Catch ex As Exception
            Debug.Print(ex.Message)
        End Try
    End Function
End Class
