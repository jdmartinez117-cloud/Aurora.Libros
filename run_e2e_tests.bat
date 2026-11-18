@echo off
setlocal
echo ===============================================================
echo   EJECUTANDO PRUEBAS END-TO-END (PLAYWRIGHT EN NAVEGADOR)
echo ===============================================================
set "PATH=C:\Program Files\nodejs;%PATH%"
call npx.cmd playwright test tests\e2e\login_and_lang.spec.js --headed
pause
