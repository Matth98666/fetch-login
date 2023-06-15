' Copyright SYRADEV - Regis TEDONE - 2023
' Compile Fetch-Login scss files

Const sourceSCSS = "./scss/"
Const destinationCSS = "../public/css/"

Set objFSO = CreateObject("Scripting.FileSystemObject")
Set objShell = CreateObject("WScript.Shell")

Set objFolder = objFSO.GetFolder(sourceSCSS)
Set objFiles = objFolder.Files

For Each objFile In objFiles
    If LCase(objFSO.GetExtensionName(objFile.Name)) = "scss" Then
        sourceFile = objFile.Name
        sourceFilenoExt = objFSO.GetBaseName(sourceFile)
        sourcefileFirstChar = Left(sourceFile, 1)
        If (sourcefileFirstChar <> "_") And (sourceFilenoExt <> "bs5") Then
            WScript.Echo "Treating " & sourceFile & ":"
            objShell.Run "sass " & sourceSCSS & sourceFile & " " & destinationCSS & sourceFilenoExt & ".min.css --style=compressed"
            WScript.Echo sourceFile & " treated."
        End If
    End If
Next

WScript.Echo "-------------------------------------"
WScript.Echo "Login SCSS Compilation done!"
