Public Class FrmLoading
    Private targetValue As Integer = 100 ' Max value for progress bar
    Private currentValue As Integer = 0
    Public Async Sub StartLoading(Optional message As String = "Loading, please wait...")
        lblMessage.Text = message
        pbLoading.Value = 0
        currentValue = 0
        Me.Show()
        Me.Refresh()

        TimerLoading.Interval = 50
        AddHandler TimerLoading.Tick, AddressOf TimerLoading_Tick
        TimerLoading.Start()
    End Sub

    Public Sub StopLoading()
        TimerLoading.Stop()
        RemoveHandler TimerLoading.Tick, AddressOf TimerLoading_Tick
        Me.Hide()
    End Sub

    Private Sub TimerLoading_Tick(sender As Object, e As EventArgs)
        If currentValue < targetValue Then
            currentValue += 1
            pbLoading.Value = currentValue
        Else
            TimerLoading.Stop()
        End If
    End Sub
End Class