: Name: Stop Server File

@echo off
udrive\home\admin\program\pskill.exe Apache.exe c

if errorlevel 2 goto :PAUSE

:PAUSE
:echo .
:pause

:END
