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
echo Sitio:  http://localhost:8080
echo Panel:  http://localhost:8080/admin
echo Cierra esta ventana o pulsa Ctrl+C para detener.
"%PHP%" -S localhost:8080 -t public public\index.php
pause
