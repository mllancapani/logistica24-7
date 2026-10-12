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
echo Usando %PHP%
echo.
"%PHP%" bin\install.php
echo.
pause
