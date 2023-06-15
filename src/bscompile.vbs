' Copyright SYRADEV - Regis TEDONE - 2023
' Compile & Compress Bootstrap scss files

Dim objShell
Set objShell = WScript.CreateObject("WScript.Shell")
objShell.Run "cmd /c sass --style=compressed ./scss/bs5.scss ../public/css/bootstrap.min.css", 0, True
WScript.Echo "-------------------------------------"
WScript.Echo "Bootstrap SCSS Compilation done!"
