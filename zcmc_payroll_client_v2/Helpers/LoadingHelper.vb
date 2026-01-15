Module LoadingHelper
    Public Async Function RunAsync(asyncAction As Func(Of Task), Optional showLoading As Boolean = True) As Task
        Dim pb As ProgressBar = Nothing

        Dim formStepper As FrmStepper = TryCast(Application.OpenForms.OfType(Of FrmStepper)().FirstOrDefault(), FrmStepper)

        If showLoading AndAlso formStepper IsNot Nothing Then
            pb = formStepper.pbLoading
            If pb IsNot Nothing Then
                pb.Minimum = 0
                pb.Maximum = 100
                pb.Style = ProgressBarStyle.Continuous
                pb.BringToFront()
                pb.Visible = True

                For i As Integer = 0 To 100 Step 10
                    pb.Value = i
                    Await Task.Delay(50) ' Small delay to visualize progress
                Next
            End If

            formStepper.pnlUserControlDisplay.Enabled = False
        End If

        Try
            Await asyncAction()
        Catch ex As Exception
            MessageBox.Show("Error: " & ex.Message)
        Finally
            If showLoading AndAlso formStepper IsNot Nothing Then
                If pb IsNot Nothing Then pb.Visible = False
                formStepper.pnlUserControlDisplay.Enabled = True
            End If
        End Try
    End Function
End Module
