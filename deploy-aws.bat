@echo off
echo Generando rutas Ziggy...
C:\xampp\php\php.exe artisan ziggy:generate

echo Compilando para produccion...
call npm run build 

echo Verificando archivos generados...
dir /b C:\xampp\htdocs\censopc\solutions\public\build\

:: Define un nombre de archivo único para evitar conflictos
set TIMESTAMP=%DATE:~6,4%%DATE:~3,2%%DATE:~0,2%_%TIME:~0,2%%TIME:~3,2%%TIME:~6,2%
set TIMESTAMP=%TIMESTAMP: =0%
set DEPLOY_ZIP=naturalbio-deploy-%TIMESTAMP%.zip

echo Creando archivo ZIP para despliegue: %DEPLOY_ZIP%...

:: Crear carpeta temporal para la lista de exclusiones
if not exist temp mkdir temp
echo node_modules\> temp\exclude.txt
echo .git\>> temp\exclude.txt
echo .github\>> temp\exclude.txt
echo .vscode\>> temp\exclude.txt
echo tests\>> temp\exclude.txt
echo vendor\>> temp\exclude.txt
echo temp\>> temp\exclude.txt

:: Usar 7-Zip si está disponible, de lo contrario usar PowerShell de forma alternativa
where 7z >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo Comprimiendo archivos con 7-Zip...
    7z a -tzip "%DEPLOY_ZIP%" * -xr@temp\exclude.txt
) else (
    echo 7-Zip no encontrado, usando PowerShell...
    
    :: Crear un script PowerShell temporal
    echo $source = Get-Location > temp\create_zip.ps1
    echo $destination = Join-Path -Path $source -ChildPath "%DEPLOY_ZIP%" >> temp\create_zip.ps1
    echo $exclude = Get-Content "temp\exclude.txt" ^| ForEach-Object { $_.Trim() } >> temp\create_zip.ps1
    echo $files = Get-ChildItem -Path $source -Recurse ^| Where-Object { >> temp\create_zip.ps1
    echo     $item = $_ >> temp\create_zip.ps1
    echo     -not ($exclude ^| Where-Object { $item.FullName -like "*\$_*" }) >> temp\create_zip.ps1
    echo } >> temp\create_zip.ps1
    echo Compress-Archive -Path {$files.FullName} -DestinationPath $destination -Force >> temp\create_zip.ps1
    
    powershell -ExecutionPolicy Bypass -File temp\create_zip.ps1
)

:: Limpiar archivos temporales
rd /s /q temp 2>nul

if exist "%DEPLOY_ZIP%" (
    echo.
    echo El archivo %DEPLOY_ZIP% ha sido creado correctamente.
    echo.
    echo IMPORTANTE: 
    echo - Asegúrate de que el directorio public/build/ está incluido
    echo - Asegúrate de que el archivo public/images/logo-icon.svg está incluido
    echo.
    echo Ahora puedes subir este archivo al servidor y extraerlo.
) else (
    echo.
    echo ERROR: No se pudo crear el archivo ZIP.
    echo Intenta crear el archivo ZIP manualmente seleccionando todos los archivos
    echo excepto node_modules, .git, vendor y otras carpetas que no necesitas en producción.
)
echo.
pause

