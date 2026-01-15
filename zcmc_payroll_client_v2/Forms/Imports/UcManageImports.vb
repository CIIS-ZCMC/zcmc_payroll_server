Public Class UcManageImports
    Private importDeductionService As New ImportDeductionService()
    Private employeeDeductionService As New EmployeeDeductionService()

    Private CurrentDeductionID As String = ""
    Private DeductionImports As New Dictionary(Of String, DataTable) ''Cache imported data here
    Public CachedEmployeeDeductionRecords As New List(Of Dictionary(Of String, Object)) ''Record is from DataGridViewLeft

    Public Function IsValid() As Boolean
        ' validate step logic
        Return True
    End Function

    Private Async Sub UcManageImports_Load(sender As Object, e As EventArgs) Handles MyBase.Load

        Await LoadingHelper.RunAsync(
            Async Function() As Task
                Await importDeductionService.GetAll(dgvDeductions, False)
            End Function,
        True)

    End Sub

    Private Async Sub dgvDeductions_CellContentClick(sender As Object, e As DataGridViewCellEventArgs) Handles dgvDeductions.CellContentClick
        If e.RowIndex < 0 Then Return ' Ignore header or invalid rows


        Dim row = dgvDeductions.Rows(e.RowIndex)
        CurrentDeductionID = row.Cells(0).Value.ToString()
        Dim deductionName = row.Cells(1).Value.ToString()

        If e.ColumnIndex = dgvDeductions.Columns("action_import").Index Then
            Dim importForm As New FrmImportFile
            importForm.lblDeductionType.Text = deductionName
            importForm.ParentFormManageDeduction = Me
            importForm.CurrentDeductionID = CurrentDeductionID
            importForm.ShowDialog()

            ' After closing import form, check if we now have data
            If DeductionImports.ContainsKey(CurrentDeductionID) Then
                LoadImportedData(DeductionImports(CurrentDeductionID))
            End If

            ' Also trigger the row selection logic after closing the form
            'Await importDeductionService.Show(dgvLeft, CInt(CurrentDeductionID), lblMessageLeft)

            Await LoadingHelper.RunAsync(
                Async Function()
                    Await importDeductionService.Show(dgvLeft, CInt(CurrentDeductionID), lblMessageLeft)
                End Function,
                True
            )

            btnCompare.Enabled = True
        Else
            ' If not import button, check for cached data
            If DeductionImports.ContainsKey(CurrentDeductionID) Then
                LoadImportedData(DeductionImports(CurrentDeductionID))
            Else
                dgvRight.Rows.Clear()
            End If

            ' trigger row selection
            Await LoadingHelper.RunAsync(
                Async Function()
                    Await importDeductionService.Show(dgvLeft, CInt(CurrentDeductionID), lblMessageLeft)
                End Function,
                True
            )
        End If
    End Sub

    ' Store data in cache
    Public Sub CacheImportedData(deductionID As String, dt As DataTable)
        If DeductionImports.ContainsKey(deductionID) Then
            DeductionImports(deductionID) = dt
        Else
            DeductionImports.Add(deductionID, dt)
        End If
    End Sub

    ' Function to receive imported data
    Public Sub LoadImportedData(dt As DataTable)
        If dt IsNot Nothing AndAlso dt.Rows.Count > 2 Then
            dgvRight.Rows.Clear()

            For i As Integer = 2 To dt.Rows.Count - 1
                dgvRight.Rows.Add(dt.Rows(i).ItemArray)
            Next
        Else
            MessageBox.Show("No data was imported.", "Warning", MessageBoxButtons.OK, MessageBoxIcon.Warning)
        End If
    End Sub

    Private Sub btnCompare_Click(sender As Object, e As EventArgs) Handles btnCompare.Click
        lblMessageLeft.Visible = False

        dgvLeft.ClearSelection()
        dgvRight.ClearSelection()

        Dim hasDiscrepancy As Boolean = False
        Dim saveRecord As Boolean = False

        If dgvLeft.Rows.Count = 0 Then
            dgvLeft.Rows.Clear()
            For Each rightRow As DataGridViewRow In dgvRight.Rows
                If Not rightRow.IsNewRow Then
                    Dim values(rightRow.Cells.Count - 1) As Object
                    For i As Integer = 0 To rightRow.Cells.Count - 1
                        values(i) = rightRow.Cells(i).Value
                    Next
                    Dim newRowIndex As Integer = dgvLeft.Rows.Add(values)
                    dgvLeft.Rows(newRowIndex).DefaultCellStyle.BackColor = System.Drawing.Color.Khaki
                End If
            Next
            saveRecord = True
            MessageBox.Show("Left data was empty. All right data copied to left.", "Info", MessageBoxButtons.OK, MessageBoxIcon.Information)
        Else
            For Each rightRow As DataGridViewRow In dgvRight.Rows
                If rightRow.IsNewRow Then Continue For

                Dim rightEmployeeNumber = rightRow.Cells(1).Value?.ToString().Trim()
                Dim rightAmount = rightRow.Cells(3).Value?.ToString().Trim()
                Dim rightTerm = rightRow.Cells(4).Value?.ToString().Trim()
                Dim rightPaid = rightRow.Cells(5).Value?.ToString().Trim()

                Dim foundMatch As Boolean = False

                For Each leftRow As DataGridViewRow In dgvLeft.Rows
                    If leftRow.IsNewRow Then Continue For

                    Dim leftEmployeeNumber = leftRow.Cells(1).Value?.ToString().Trim()
                    Dim leftAmount = leftRow.Cells(3).Value?.ToString().Trim()
                    Dim leftTerm = leftRow.Cells(4).Value?.ToString().Trim()
                    Dim leftPaid = leftRow.Cells(5).Value?.ToString().Trim()

                    If rightEmployeeNumber = leftEmployeeNumber Then
                        foundMatch = True

                        Dim rightAmountDecimal As Decimal
                        Dim leftAmountDecimal As Decimal

                        ' Compare amount, term, and paid
                        Dim amountDiscrepancy As Boolean = False
                        Dim termDiscrepancy As Boolean = False
                        Dim paidDiscrepancy As Boolean = False

                        If Decimal.TryParse(rightAmount, rightAmountDecimal) AndAlso Decimal.TryParse(leftAmount, leftAmountDecimal) Then
                            If rightAmountDecimal <> leftAmountDecimal Then
                                amountDiscrepancy = True
                            End If
                        Else
                            amountDiscrepancy = (rightAmount <> leftAmount)
                        End If

                        termDiscrepancy = (rightTerm <> leftTerm)
                        paidDiscrepancy = (rightPaid <> leftPaid)

                        If amountDiscrepancy Or termDiscrepancy Or paidDiscrepancy Then
                            leftRow.DefaultCellStyle.BackColor = System.Drawing.Color.IndianRed
                            hasDiscrepancy = True
                        Else
                            leftRow.DefaultCellStyle.BackColor = System.Drawing.Color.White
                        End If

                        Exit For
                    End If
                Next

                If Not foundMatch Then
                    ' Add missing row from right to left
                    Dim values(rightRow.Cells.Count - 1) As Object
                    For i As Integer = 0 To rightRow.Cells.Count - 1
                        values(i) = rightRow.Cells(i).Value
                    Next
                    Dim newRowIndex = dgvLeft.Rows.Add(values)
                    dgvLeft.Rows(newRowIndex).DefaultCellStyle.BackColor = System.Drawing.Color.White
                End If
            Next
        End If

        ' Enable buttons based on status
        btnAcceptChanges.Enabled = hasDiscrepancy
        btnSaveRecords.Enabled = Not hasDiscrepancy

        ' Cache valid data only when there’s no discrepancy
        If Not hasDiscrepancy Then
            Dim activePayroll = AppState.PayrollPeriod
            Dim payrollPeriodID = AppState.PayrollPeriodId

            CachedEmployeeDeductionRecords.Clear()
            For Each row As DataGridViewRow In dgvLeft.Rows
                If row.IsNewRow Then Continue For

                Dim requestData As New Dictionary(Of String, Object) From {
                    {"payroll_period_id", payrollPeriodID},
                    {"deduction_id", CurrentDeductionID},
                    {"employee_number", row.Cells(1).Value?.ToString()},
                    {"amount", row.Cells(3).Value},
                    {"total_term", row.Cells(4).Value},
                    {"total_paid", row.Cells(5).Value}
                }

                CachedEmployeeDeductionRecords.Add(requestData)
            Next
        End If
    End Sub

    Private Sub btnAcceptChanges_Click(sender As Object, e As EventArgs) Handles btnAcceptChanges.Click
        Dim saveRecord As Boolean = False

        ' Loop through each row in dgvRight (imported data)
        For Each rightRow As DataGridViewRow In dgvRight.Rows
            If rightRow.IsNewRow Then Continue For

            Dim rightEmployeeNumber = rightRow.Cells(1).Value?.ToString().Trim()
            Dim rightAmount = rightRow.Cells(3).Value?.ToString().Trim()
            Dim rightTerm = rightRow.Cells(4).Value?.ToString().Trim()
            Dim rightPaid = rightRow.Cells(5).Value?.ToString().Trim()

            Dim foundMatch As Boolean = False

            For Each leftRow As DataGridViewRow In dgvLeft.Rows
                If leftRow.IsNewRow Then Continue For

                Dim leftEmployeeNumber = leftRow.Cells(1).Value?.ToString().Trim()
                Dim leftAmount = leftRow.Cells(3).Value?.ToString().Trim()
                Dim leftTerm = leftRow.Cells(4).Value?.ToString().Trim()
                Dim leftPaid = leftRow.Cells(5).Value?.ToString().Trim()

                If rightEmployeeNumber = leftEmployeeNumber Then
                    foundMatch = True

                    Dim rightAmountDecimal As Decimal
                    Dim leftAmountDecimal As Decimal
                    Dim amountChanged As Boolean = False
                    Dim termChanged As Boolean = False
                    Dim paidChanged As Boolean = False

                    ' Update amount if different
                    If Decimal.TryParse(rightAmount, rightAmountDecimal) AndAlso Decimal.TryParse(leftAmount, leftAmountDecimal) Then
                        If Not Object.Equals(leftAmountDecimal, rightAmountDecimal) Then
                            leftRow.Cells(3).Value = rightAmount
                            amountChanged = True
                        End If
                    ElseIf rightAmount <> leftAmount Then
                        leftRow.Cells(3).Value = rightAmount
                        amountChanged = True
                    End If

                    ' Update term if different
                    If rightTerm <> leftTerm Then
                        leftRow.Cells(4).Value = rightTerm
                        termChanged = True
                    End If

                    ' Update paid if different
                    If rightPaid <> leftPaid Then
                        leftRow.Cells(5).Value = rightPaid
                        paidChanged = True
                    End If

                    If amountChanged Or termChanged Or paidChanged Then
                        leftRow.DefaultCellStyle.BackColor = System.Drawing.Color.LimeGreen
                        saveRecord = True
                    Else
                        leftRow.DefaultCellStyle.BackColor = System.Drawing.Color.White
                    End If

                    Exit For
                End If
            Next

            ' If no match, add new row to dgvLeft and make it yellow
            If Not foundMatch Then
                Dim values(rightRow.Cells.Count - 1) As Object
                For i As Integer = 0 To rightRow.Cells.Count - 1
                    values(i) = rightRow.Cells(i).Value
                Next
                Dim newRowIndex As Integer = dgvLeft.Rows.Add(values)
                dgvLeft.Rows(newRowIndex).DefaultCellStyle.BackColor = System.Drawing.Color.Khaki
            End If
        Next

        ' Set all other rows (not changed) to white
        For Each leftRow As DataGridViewRow In dgvLeft.Rows
            If leftRow.IsNewRow Then Continue For
            If leftRow.DefaultCellStyle.BackColor <> System.Drawing.Color.LimeGreen AndAlso leftRow.DefaultCellStyle.BackColor <> System.Drawing.Color.Khaki Then
                leftRow.DefaultCellStyle.BackColor = System.Drawing.Color.White
            End If
        Next

        btnSaveRecords.Enabled = saveRecord
        If saveRecord = True Then
            btnCompare.Enabled = False
            btnAcceptChanges.Enabled = False

            Dim activePayroll = AppState.PayrollPeriod
            Dim payrollPeriodID = AppState.PayrollPeriodId

            CachedEmployeeDeductionRecords.Clear()
            For Each row As DataGridViewRow In dgvLeft.Rows
                If row.IsNewRow Then Continue For

                Dim requestData As New Dictionary(Of String, Object) From {
                    {"employee_number", row.Cells(1).Value?.ToString()},
                    {"deduction_id", CurrentDeductionID},
                    {"amount", row.Cells(3).Value},
                    {"total_term", row.Cells(4).Value},
                    {"total_paid", row.Cells(5).Value}
                }

                CachedEmployeeDeductionRecords.Add(requestData)
            Next

            MessageBox.Show("Changes accepted. Amounts updated from imported data.", "Success", MessageBoxButtons.OK, MessageBoxIcon.Information)
        End If
    End Sub

    Private Async Sub btnSaveRecords_Click(sender As Object, e As EventArgs) Handles btnSaveRecords.Click
        Dim result As ServiceResult = Nothing

        Await LoadingHelper.RunAsync(
            Async Function()
                result = Await employeeDeductionService.CreateBulk(CachedEmployeeDeductionRecords)
            End Function,
            showLoading:=True
        )

        ' Safety check (optional but recommended)
        If result Is Nothing Then Return

        MessageBox.Show(
            result.Message,
            If(result.Success, "Success", "Error"),
            MessageBoxButtons.OK,
            If(result.Success, MessageBoxIcon.Information, MessageBoxIcon.Error)
        )
    End Sub

End Class
