' Copyright SYRADEV - Regis TEDONE - 2023
' Compress Fetch-Login JS files

Const sourceJS = "./js/"
Const destinationJS = "../public/js/"
Const uglify = "./node_modules/uglify-js/bin/uglifyjs"

Set objFSO = CreateObject("Scripting.FileSystemObject")
Set objShell = CreateObject("WScript.Shell")

Set objFolder = objFSO.GetFolder(sourceJS)
Set objFiles = objFolder.Files

For Each objFile In objFiles
    If LCase(objFSO.GetExtensionName(objFile.Name)) = "js" Then
        sourceFile = objFile.Name
        sourceFilenoExt = objFSO.GetBaseName(sourceFile)
        'WScript.Echo "Treating " & sourceFile & ":"
        objShell.Run "cmd /c cd " & sourceJS & " && " & uglify & " " & sourceFile & " -o " & destinationJS & sourceFilenoExt & ".min.js -c -m --comments '/syradev/' --source-map ""root='https://www.mysimplon.org/fetch_login/src/js/',url='" & sourceFilenoExt & ".min.js.map""", 0, True
        'WScript.Echo sourceFile & " treated."
    End If
Next

WScript.Echo "----------------------------------"
WScript.Echo "JS Compression done!"
