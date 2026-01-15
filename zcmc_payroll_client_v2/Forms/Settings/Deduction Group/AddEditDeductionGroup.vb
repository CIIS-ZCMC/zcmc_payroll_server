Public Class AddEditDeductionGroup
    Public action As String

    Public _DeductionGroupID As String
    Public _DeductionGroupName As String
    Public _DeductionGroupCode As String
    Private Sub ClearInput(root As Control)
        For Each ctrl As Control In root.Controls
            ClearInput(ctrl)
            If TypeOf ctrl Is TextBox Then
                CType(ctrl, TextBox).Text = String.Empty
            End If

            If TypeOf ctrl Is ComboBox Then
                CType(ctrl, ComboBox).Text = String.Empty
            End If

            If TypeOf ctrl Is RadioButton Then
                CType(ctrl, RadioButton).Checked = False
            End If

            If TypeOf ctrl Is NumericUpDown Then
                CType(ctrl, NumericUpDown).Value = 0
            End If

            If TypeOf ctrl Is CheckBox Then
                CType(ctrl, CheckBox).Checked = False
            End If
        Next ctrl
    End Sub

    Private Sub SetDetails()
        _DeductionGroupName = txtName.Text
        _DeductionGroupCode = txtCode.Text
    End Sub

    Private Sub AddEditDeductionGroup_Load(sender As Object, e As EventArgs) Handles MyBase.Load

    End Sub

    Private Async Sub btnSave_Click(sender As Object, e As EventArgs) Handles btnSave.Click
        SetDetails()
        If action = "add" Then

        ElseIf action = "edit" Then

        End If


    End Sub
End Class