Public Class FrmPayrollType
    Private Sub btnRegularPayroll_Click(sender As Object, e As EventArgs) Handles btnRegularPayroll.Click
        AppState.PayrollType = "regular"
    End Sub

    Private Sub btnJobOrderPayroll_Click(sender As Object, e As EventArgs) Handles btnJobOrderPayroll.Click
        AppState.PayrollType = "job_order"
    End Sub

    Private Sub btnNightDifferential_Click(sender As Object, e As EventArgs) Handles btnNightDifferential.Click
        AppState.PayrollType = "night_differential"
    End Sub

    Private Sub btnSpecialPayroll_Click(sender As Object, e As EventArgs) Handles btnSpecialPayroll.Click
        AppState.PayrollType = "special"
    End Sub

    Private Sub btn13MonthPay_Click(sender As Object, e As EventArgs) Handles btn13MonthPay.Click
        AppState.PayrollType = "13_month_pay"
    End Sub
End Class