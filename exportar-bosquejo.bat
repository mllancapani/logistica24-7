@echo off
setlocal
set "PHP="
for /d %%i in (C:\laragon\bin\php\php-*) do set "PHP=%%i\php.exe"
if not defined PHP (
  echo No se encontro PHP en C:\laragon\bin\php
  pause
  exit /b 1
)
cd /d "%~dp0"
echo Recuerda: iniciar-local.bat debe estar abierto.
"%PHP%" bin\export-static.php http://localhost:8080
pause
