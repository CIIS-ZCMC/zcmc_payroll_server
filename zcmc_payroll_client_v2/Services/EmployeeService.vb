Public Class EmployeeService
    Public Async Function GetEmployee(dgv As DataGridView, type As String, paginate As Boolean, Optional perPage As Integer = 15, Optional page As Integer = 1) As Task(Of EmployeeResponse)
        Try
            dgv.Rows.Clear()

            Dim response = Await EmployeeApi.GetAll(type, paginate, perPage, page)

            If response Is Nothing OrElse response.data Is Nothing Then Exit Function

            Dim i As Integer = 1
            For Each data As EmployeeResponse In response.data
                Dim reason As String = If(data.excluded?.reason, "")

                dgv.Rows.Add(i, data.id, data.employee_number, data.full_name, data.designation, data.assigned_area.details.name, reason, data.status,
                             "View", "More", "Manage Deduction", "Manage Receivable")
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

                dgv.Rows.Add(i, item.id, item.deduction.name, value)

                i += 1
            Next

            dgv.Refresh()
            Return Nothing
        Catch ex As Exception
            Debug.Print(ex.Message)
        End Try
    End Function

    Public Async Function Show(dgv As DataGridView, id As Integer) As Task(Of EmployeeResponse)
        Try
            dgv.Rows.Clear()
            Dim response = Await EmployeeApi.Show(id)

            If response Is Nothing OrElse response.data Is Nothing Then Exit Function

            Dim data As EmployeeResponse = response.data

            Dim i As Integer = 1
            For Each item In data.deductions
                Dim value As String = If(item?.amount, item.percentage)
                dgv.Rows.Add(i, item.id, item.payroll_period_id, item.id, item.deduction_id, item.deduction.name, item.deduction.code,
                             item.amount, item.percentage, item.completed_at, item.with_terms, item.total_term, item.total_paid, item.frequency,
                             item.reason, item.is_default, "Edit", "Stop")
                i += 1
            Next

            dgv.Refresh()
            Return Nothing
        Catch ex As Exception
            Debug.Print(ex.Message)
        End Try
    End Function
End Class
