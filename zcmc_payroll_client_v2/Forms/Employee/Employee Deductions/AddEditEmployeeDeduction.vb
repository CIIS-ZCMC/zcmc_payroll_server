Public Class AddEditEmployeeDeduction
    Dim helper As New Helpers

    Private service As New EmployeeDeductionService
    Private serviceDeduction As New DeductionService

    Public _employeeName As String

    Public add As Boolean = False
    Public edit As Boolean = False

    Public _employeeDeductionID As String
    Public _payroll_period_id As Integer
    Public _employeeID As Integer
    Public _deductionID As Integer
    Public _deductionCode As String
    Public _billingCycle As String
    Public _withTerms As Boolean
    Public _totalTerm As Integer
    Public _termPaid As Integer
    Public _amount As Decimal
    Public _percentage As Decimal
    Public _reason As String
    Public _isDefault As Integer

    Public _dgv As DataGridView

    Private Sub btnCancel_Click(sender As Object, e As EventArgs) Handles btnCancel.Click
        Me.DialogResult = DialogResult.Cancel
        Me.Close()
    End Sub

    Private Async Sub btnNext_Click(sender As Object, e As EventArgs) Handles btnNext.Click
        Dim dto As EmployeeDeductionDto

        Try
            dto = ValidateAndBuild()
        Catch ex As Exception
            MessageBox.Show(ex.Message)
            Return
        End Try

        Dim result As ServiceResult = Nothing

        If add = True Then
            result = Await service.CreateSingle(dto)
        ElseIf edit = True Then
            result = Await service.Update(_employeeDeductionID, dto, "toUpdate")
        End If

        ' Safety check (optional but recommended)
        If result Is Nothing Then Return

        MessageBox.Show(
            result.Message,
            If(result.Success, "Success", "Error"),
            MessageBoxButtons.OK,
            If(result.Success, MessageBoxIcon.Information, MessageBoxIcon.Error)
        )

        Me.DialogResult = DialogResult.OK
    End Sub

    Private Async Sub AddEditEmployeeDeduction_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        Dim list = Await serviceDeduction.DropDownDeduction()
        helper.BindDropDown(cmbDeductions, list, " ")

        If edit = True Then
            Dim row = _dgv.SelectedRows(0)

            _employeeDeductionID = row.Cells(1).Value
            _payroll_period_id = row.Cells(2).Value
            _employeeID = row.Cells(3).Value
            _deductionID = row.Cells(4).Value
            _amount = row.Cells(7).Value
            _percentage = row.Cells(8).Value
            _withTerms = row.Cells(10).Value
            _totalTerm = row.Cells(11).Value
            _termPaid = row.Cells(12).Value
            _billingCycle = row.Cells(13).Value
            _reason = row.Cells(14).Value
            _isDefault = row.Cells(15).Value

            cmbDeductions.Enabled = False

            '' Match the loaded _deductionID to cmbDeductions
            For Each item As Object In cmbDeductions.Items
                Dim deductionItem As ComboItem = TryCast(item, ComboItem)
                If deductionItem IsNot Nothing AndAlso deductionItem.id = _deductionID Then
                    cmbDeductions.SelectedItem = deductionItem
                    Exit For
                End If
            Next

            ' Match the loaded _billingCycle to cmbBillingCycle
            For Each item As Object In cmbBillingCycle.Items
                If item IsNot Nothing AndAlso item.ToString().ToLower() = _billingCycle.ToLower() Then
                    cmbBillingCycle.SelectedItem = item
                    Exit For
                End If
            Next

            If _withTerms = True Then
                cbHasTerm.Checked = True
                txtTerm.Text = _totalTerm
            End If

            If _amount > 0 AndAlso _percentage > 0 Then
                rdbPercentage.Checked = True
                txtSalaryPercentage.Text = _percentage
            ElseIf _isDefault = 1 Then
                rdbDefault.Checked = True
                txtDefaultAmount.Text = _amount
            Else
                rdbPreferred.Checked = True
                txtPreferredAmount.Text = _amount
            End If

            txtReason.Text = _reason
        End If
    End Sub

    Private Sub cbHasTerm_CheckedChanged(sender As Object, e As EventArgs) Handles cbHasTerm.CheckedChanged
        If cbHasTerm.Checked = True Then
            _withTerms = True
            lblTerms.Enabled = True
            txtTerm.Enabled = True
        Else
            _withTerms = False
            lblTerms.Enabled = False
            txtTerm.Enabled = False
        End If
    End Sub

    Private Sub rdbDefault_CheckedChanged(sender As Object, e As EventArgs) Handles rdbDefault.CheckedChanged
        If rdbDefault.Checked = True Then
            txtDefaultAmount.Enabled = True
        Else
            txtDefaultAmount.Enabled = False
            txtDefaultAmount.Text = 0
        End If
    End Sub

    Private Sub rdbPercentage_CheckedChanged(sender As Object, e As EventArgs) Handles rdbPercentage.CheckedChanged
        If rdbPercentage.Checked = True Then
            txtSalaryPercentage.Enabled = True
        Else
            txtSalaryPercentage.Enabled = False
            txtSalaryPercentage.Text = 0
        End If
    End Sub

    Private Sub rdbPreferred_CheckedChanged(sender As Object, e As EventArgs) Handles rdbPreferred.CheckedChanged
        If rdbPreferred.Checked = True Then
            txtPreferredAmount.Enabled = True
        Else
            txtPreferredAmount.Enabled = False
            txtPreferredAmount.Text = 0
        End If
    End Sub

    Private Sub txtDefaultAmount_TextChanged(sender As Object, e As EventArgs) Handles txtDefaultAmount.TextChanged
        FilterNumericInput(txtDefaultAmount, allowDecimals:=False)
    End Sub

    Private Sub txtSalaryPercentage_TextChanged(sender As Object, e As EventArgs) Handles txtSalaryPercentage.TextChanged
        FilterNumericInput(txtSalaryPercentage, allowDecimals:=True)
    End Sub

    Private Sub txtPreferredAmount_TextChanged(sender As Object, e As EventArgs) Handles txtPreferredAmount.TextChanged
        FilterNumericInput(txtPreferredAmount, allowDecimals:=True)
    End Sub
    Private Sub FilterNumericInput(textBox As TextBox, allowDecimals As Boolean)
        Dim newText As New System.Text.StringBuilder
        Dim decimalPointCount As Integer = 0

        For Each c As Char In textBox.Text
            ' Allow digits (0-9)
            If Char.IsDigit(c) Then
                newText.Append(c)
                ' Allow ONE decimal point (if allowed)
            ElseIf allowDecimals AndAlso c = "." AndAlso decimalPointCount = 0 Then
                newText.Append(c)
                decimalPointCount += 1
            End If
        Next

        ' Add leading zero if text starts with "." (e.g., ".5" → "0.5")
        If allowDecimals AndAlso newText.ToString().StartsWith(".") Then
            newText.Insert(0, "0")
        End If

        ' Update text if changed
        If textBox.Text <> newText.ToString() Then
            Dim cursorPos As Integer = textBox.SelectionStart
            textBox.Text = newText.ToString()
            textBox.SelectionStart = Math.Min(cursorPos, textBox.Text.Length)
        End If
    End Sub

    Private Function ValidateAndBuild() As EmployeeDeductionDto

        If cmbDeductions.SelectedValue Is Nothing Then
            Throw New Exception("Please select a deduction.")
        End If

        If cmbBillingCycle.SelectedItem Is Nothing Then
            Throw New Exception("Please select a billing cycle.")
        End If

        If cbHasTerm.Checked Then
            If String.IsNullOrWhiteSpace(txtTerm.Text) OrElse Not Integer.TryParse(txtTerm.Text, Nothing) OrElse Integer.Parse(txtTerm.Text) <= 0 Then
                Throw New Exception("Please enter a valid total term.")
            End If
        End If

        Dim dto As New EmployeeDeductionDto With {
            .EmployeeId = _employeeID,
            .DeductionId = CInt(cmbDeductions.SelectedValue),
            .BillingCycle = cmbBillingCycle.Text.ToLower(),
            .WithTerms = cbHasTerm.Checked,
            .Reason = txtReason.Text,
            .IsDefault = rdbDefault.Checked
        }

        If cbHasTerm.Checked Then
            dto.TotalTerm = Integer.Parse(txtTerm.Text)
        End If

        If rdbPercentage.Checked Then
            dto.Percentage = Decimal.Parse(txtSalaryPercentage.Text)
        Else
            dto.Amount = Decimal.Parse(
            If(rdbDefault.Checked, txtDefaultAmount.Text, txtPreferredAmount.Text)
        )
        End If

        Return dto
    End Function

End Class