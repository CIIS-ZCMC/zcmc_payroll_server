Imports System.IO
Imports System.Text
Imports ExcelInterop = Microsoft.Office.Interop.Excel
Imports Microsoft.VisualBasic.FileIO

Public Class FrmImportFile
    Dim EmployeeDeductionResponse As New EmployeeDeductionResponse

    Public helper As New Helpers

    Public Property CurrentDeductionID As String
    Public ParentFormManageDeduction As UcManageImports
    Private Sub FrmImportFile_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        Dim activePayroll = AppState.PayrollPeriod
        Dim monthNumber As Integer = Convert.ToInt32(activePayroll.month)
        Dim monthName As String = helper.GetMonthName(monthNumber)
        lblMonth.Text = monthName
    End Sub

    Private Sub btnBrowser_Click(sender As Object, e As EventArgs) Handles btnBrowser.Click
        Dim openFileDialog As New OpenFileDialog()

        ' Set filter to only allow CSV files
        openFileDialog.Filter = "Excel files (*.xlsx;*.xls)|*.xlsx;*.xls|CSV files (*.csv)|*.csv"
        openFileDialog.Title = "Select a CSV, XLSX, or XLS File"

        ' Show the dialog and check if the user selected a file
        If openFileDialog.ShowDialog() = DialogResult.OK Then
            ' Get the file path
            Dim selectedFile As String = openFileDialog.FileName

            Dim extension As String = Path.GetExtension(selectedFile).ToLower()
            ' Check if the file has a .csv extension (optional safety check)
            If extension = ".csv" OrElse extension = ".xlsx" OrElse extension = ".xls" Then
                ' For example, store the file path
                txtFile.Text = selectedFile
                EmployeeDeductionResponse.FilePath = selectedFile
            Else
                MessageBox.Show("Please select a valid CSV file.")
            End If
        End If
    End Sub

    Private Sub btnImport_Click(sender As Object, e As EventArgs) Handles btnImport.Click
        Dim filePath As String = EmployeeDeductionResponse.FilePath
        If String.IsNullOrEmpty(filePath) Then
            MessageBox.Show("Please select a file before importing.", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
            Return
        End If

        Dim extension As String = Path.GetExtension(filePath).ToLower()
        Dim dt As DataTable

        If extension = ".csv" Then
            dt = ReadCSV(filePath)
        ElseIf extension = ".xlsx" OrElse extension = ".xls" Then
            Dim tempCsvPath As String = Path.Combine(Path.GetTempPath(), Guid.NewGuid().ToString() & ".csv")
            ConvertExcelToCsv(filePath, tempCsvPath)

            '  Read the CSV into DataTable
            dt = ReadCSV(tempCsvPath)

            ' Optionally delete temp file
            If File.Exists(tempCsvPath) Then
                File.Delete(tempCsvPath)
            End If
        Else
            MessageBox.Show("Invalid file format.", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
            Return
        End If

        ' Pass the data to the parent form
        If ParentFormManageDeduction IsNot Nothing Then
            ParentFormManageDeduction.CacheImportedData(CurrentDeductionID, dt)
            Me.Close()
        Else
            MessageBox.Show("Failed to send data to the main form.", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
        End If
    End Sub

    Private Function ReadCSV(filePath As String) As DataTable
        Dim dt As New DataTable()
        Try
            Using reader As New TextFieldParser(filePath, Encoding.UTF8)
                reader.TextFieldType = FieldType.Delimited
                reader.SetDelimiters(",")

                reader.HasFieldsEnclosedInQuotes = True

                ' Read header
                If Not reader.EndOfData Then
                    Dim headers As String() = reader.ReadFields()
                    For Each header As String In headers
                        dt.Columns.Add(header.Trim())
                    Next
                End If

                ' Read rows
                While Not reader.EndOfData
                    Dim fields As String() = reader.ReadFields()

                    ' If row is shorter or longer, adjust accordingly
                    If fields.Length <> dt.Columns.Count Then
                        Array.Resize(fields, dt.Columns.Count)
                    End If

                    dt.Rows.Add(fields)
                End While
            End Using
        Catch ex As Exception
            MessageBox.Show("Error reading CSV file: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
        End Try
        Return dt
    End Function

    Private Sub ConvertExcelToCsv(excelPath As String, csvPath As String)
        Dim app As New ExcelInterop.Application()
        Dim workbook As ExcelInterop.Workbook = app.Workbooks.Open(excelPath)
        Dim worksheet As ExcelInterop.Worksheet = workbook.Sheets(1)
        worksheet.SaveAs(csvPath, ExcelInterop.XlFileFormat.xlCSV)
        workbook.Close(False)
        app.Quit()
    End Sub

    Private Sub btnCancel_Click(sender As Object, e As EventArgs) Handles btnCancel.Click
        Me.Close()
    End Sub
End Class