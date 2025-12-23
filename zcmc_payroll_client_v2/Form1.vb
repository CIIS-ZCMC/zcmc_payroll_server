Imports MaterialSkin

Public Class Form1


    Private isPanel2Open As Boolean = False
    Private targetPanel2Width As Integer = 280
    Private slideSpeed As Integer = 20

    Private Sub Form1_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        SplitContainer1.Panel2Collapsed = True

        dgvTable.Rows.Add("1", "1", "EMP-001", "Juan Dela Cruz", "Admin Aide III", "HRMO", Nothing, "Included", "View", "Exclude", "Manage Deductions", "Manage Receivables")
        dgvTable.Rows.Add("2", "2", "EMP-002", "Maria Santos", "Accounting I", "Finance", Nothing, "Included", "View", "Exclude", "Manage Deductions", "Manage Receivables")
        dgvTable.Rows.Add("3", "3", "EMP-003", "Pedro Reyes", "Computer Programmer I", "IISU", Nothing, "Included", "View", "Exclude", "Manage Deductions", "Manage Receivables")
    End Sub

    Private Sub dgvTable_CellContentClick(sender As Object, e As DataGridViewCellEventArgs) Handles dgvTable.CellContentClick
        If e.RowIndex < 0 Then Exit Sub

        If e.ColumnIndex = 8 Then
            Dim row = dgvTable.Rows(e.RowIndex)

            ' Pass data to Panel2 labels
            lblId.Text = row.Cells(0).Value.ToString()
            lblName.Text = row.Cells(1).Value.ToString()
            lblDepartment.Text = row.Cells(2).Value.ToString()

            ' Open Panel2
            If Not isPanel2Open AndAlso Not tmrSlide.Enabled Then
                tmrSlide.Start()
            End If
        End If
    End Sub

    Private Sub btnClose_Click(sender As Object, e As EventArgs) Handles btnClose.Click
        If isPanel2Open AndAlso Not tmrSlide.Enabled Then
            tmrSlide.Start()
        End If
    End Sub

    Private Sub tmrSlide_Tick(sender As Object, e As EventArgs) Handles tmrSlide.Tick
        Dim maxSplitterDistance As Integer =
          SplitContainer1.Width - targetPanel2Width

        If Not isPanel2Open Then
            ' OPEN Panel2
            SplitContainer1.Panel2Collapsed = False

            If SplitContainer1.SplitterDistance + slideSpeed >= maxSplitterDistance Then
                SplitContainer1.SplitterDistance = maxSplitterDistance
                isPanel2Open = True
                tmrSlide.Stop()
            Else
                SplitContainer1.SplitterDistance += slideSpeed
            End If

        Else
            ' CLOSE Panel2
            If SplitContainer1.SplitterDistance - slideSpeed <= SplitContainer1.Width - 1 Then
                SplitContainer1.SplitterDistance = SplitContainer1.Width - 1
                SplitContainer1.Panel2Collapsed = True
                isPanel2Open = False
                tmrSlide.Stop()
            Else
                SplitContainer1.SplitterDistance -= slideSpeed
            End If
        End If
    End Sub
End Class
