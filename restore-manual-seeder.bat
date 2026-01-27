@echo off
echo ========================================
echo RESTAURAR MANUAL SEEDER - BACKUP
echo ========================================
echo.
echo Este script restaurará el ManualSeeder a su versión anterior
echo.
pause

echo Restaurando archivo...
copy /Y database\seeders\ManualSeeder.php.backup database\seeders\ManualSeeder.php

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ✓ Archivo restaurado exitosamente
    echo.
    echo Ahora ejecuta: php artisan db:seed --class=ManualSeeder
    echo.
) else (
    echo.
    echo ✗ Error al restaurar el archivo
    echo.
)

pause
