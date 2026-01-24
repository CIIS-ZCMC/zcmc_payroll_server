Public Class EmployeeAdjustmentService
    Public Async Function GetEmployeeAdjustment(dgv As DataGridView, type As String, paginate As Boolean, Optional perPage As Integer = 15,
                                                Optional page As Integer = 1) As Task(Of EmployeeAdjustmentResponse)
        Try
            dgv.Rows.Clear()

            Dim response = Await EmployeeAdjustmentApi.GetAll(AppState.PayrollPeriodId, type, paginate, perPage, page)

            If response Is Nothing OrElse response.data Is Nothing Then Exit Function

            Dim i As Integer = 1
            For Each data As EmployeeAdjustmentResponse In response.data
                Dim btnIncludeExclude As String = If(type = "isIncluded", "Exclude", "Include")
                dgv.Rows.Add(i, data.id, data.employee_number, data.full_name, data.designation, data.assigned_area.details.name, data.reason, data.status,
                             "View", btnIncludeExclude, "Manage Deduction", "Manage Receivable")
                i += 1
            Next

            dgv.Refresh()

            Return Nothing
        Catch ex As Exception
            Debug.Print(ex.Message)
        End Try
    End Function

    ''For Payroll Step Two
    Public Async Function GetEmployeeAdjustmentWithCheckbox(dgv As DataGridView, lbl As Label, type As String, paginate As Boolean,
                                                            Optional perPage As Integer = 15, Optional page As Integer = 1) As Task(Of EmployeeAdjustmentResponse)
        Try
            dgv.Rows.Clear()
            lbl.Visible = False

            Dim response = Await EmployeeAdjustmentApi.GetAll(AppState.PayrollPeriodId, type, paginate, perPage, page)

            If response Is Nothing OrElse response.data Is Nothing Then
                lbl.Visible = True
                lbl.Text = "No data is found."
            End If

            Dim i As Integer = 1
            For Each data As EmployeeAdjustmentResponse In response.data
                dgv.Rows.Add(False, i, data.id, data.employee_number, data.full_name, data.designation, data.assigned_area.details.name)
                i += 1
            Next

            dgv.Refresh()

            Return Nothing
        Catch ex As Exception
            Debug.Print(ex.Message)
        End Try
    End Function
End Class
