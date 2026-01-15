Public Class DeductionService
    Public Async Function DropDownDeduction() As Task(Of List(Of ComboItem))
        Try
            Dim response = Await DeductionApi.GetAll(False)

            If response Is Nothing OrElse response.data Is Nothing Then Exit Function

            Return response.data.Select(Function(d) New ComboItem With {
                .Id = d.id,
                .Text = d.code
            }).ToList()

        Catch ex As Exception
            Debug.Print(ex.Message)
        End Try
    End Function
End Class
