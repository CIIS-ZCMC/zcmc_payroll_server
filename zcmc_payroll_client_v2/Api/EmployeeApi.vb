Public Class EmployeeApi
    Public Shared Async Function GetAll(Optional type As String = "", Optional paginate As Boolean = True, Optional perPage As Integer = 15, Optional page As Integer = 1) As Task(Of ApiResponse(Of List(Of EmployeeResponse)))
        Dim endpoint As String = $"{urlEmployee}?type={type}&paginate={paginate.ToString().ToLower()}&per_page={perPage}&page={page}"
        Return Await ApiClient.GetAsync(Of List(Of EmployeeResponse))(endpoint)
    End Function

    Public Shared Async Function Create(data As Object) As Task(Of ApiResponse(Of EmployeeResponse))
        Return Await ApiClient.PostAsync(Of EmployeeResponse)(urlEmployee, data)
    End Function

    Public Shared Async Function Show(id As Integer) As Task(Of ApiResponse(Of EmployeeResponse))
        Return Await ApiClient.GetAsync(Of EmployeeResponse)($"{urlEmployee}/{id}")
    End Function

    Public Shared Async Function Update(id As Integer, data As Object) As Task(Of ApiResponse(Of EmployeeResponse))
        Return Await ApiClient.PutAsync(Of EmployeeResponse)($"{urlEmployee}/{id}", data)
    End Function

    Public Shared Async Function Delete(id As Integer) As Task(Of ApiResponse(Of String))
        Return Await ApiClient.DeleteAsync(Of String)($"{urlEmployee}/{id}")
    End Function
End Class
