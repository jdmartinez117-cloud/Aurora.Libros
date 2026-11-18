@echo off
setlocal
echo ===============================================================
echo   EJECUTANDO SUITE DE PRUEBAS AUTOMATIZADA - AURORA.LIBROS
echo ===============================================================
if exist "C:\xampp\php\php.exe" (
    "C:\xampp\php\php.exe" tests\run_tests.php
) else (
    php tests\run_tests.php
)
pause
