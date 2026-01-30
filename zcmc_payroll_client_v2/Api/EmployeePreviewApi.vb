Public Class EmployeePreviewApi
    Public Shared Async Function GetPreview(payroll_period_id As Integer, type As String,
                                            Optional selected_employees As List(Of Integer) = Nothing,
                                            Optional perPage As Integer = 15, Optional page As Integer = 1
                                            ) As Task(Of ApiResponse(Of List(Of EmployeePreviewResponse)))

        'Dim endpoint As String = $"{urlEmployeePreview}?payroll_period_id={payroll_period_id}&type={type}&selected_employees={selected_employees}&per_page={perPage}&page={page}"

        Dim query As New List(Of String) From {
            $"payroll_period_id={payroll_period_id}",
            $"type={type}",
            $"per_page={perPage}",
            $"page={page}"
        }

        If selected_employees IsNot Nothing AndAlso selected_employees.Count > 0 Then
            For Each id In selected_employees
                query.Add($"selected_employees[]={id}")
            Next
        End If

        Dim endpoint As String = $"{urlEmployeePreview}?" & String.Join("&", query)
        Return Await ApiClient.GetAsync(Of List(Of EmployeePreviewResponse))(endpoint)
    End Function
End Class
