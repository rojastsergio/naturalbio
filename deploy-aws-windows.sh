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

:: Crear directorio temporal para la implementación
if not exist temp mkdir temp
cd temp

:: Copiar los archivos necesarios al directorio temporal
echo Copiando archivos al directorio temporal...
xcopy /E /I /Y ..\app app
xcopy /E /I /Y ..\bootstrap bootstrap
xcopy /E /I /Y ..\config config
xcopy /E /I /Y ..\database database
xcopy /E /I /Y ..\Modules Modules
xcopy /E /I /Y ..\public public
xcopy /E /I /Y ..\resources resources
xcopy /E /I /Y ..\routes routes
xcopy /E /I /Y ..\storage storage
copy ..\artisan artisan
copy ..\composer.json composer.json
copy ..\composer.lock composer.lock
copy ..\phpunit.xml phpunit.xml
copy ..\package.json package.json
copy ..\tailwind.config.js tailwind.config.js
copy ..\vite.config.js vite.config.js

:: Asegurarse de que las carpetas public/build y public/images existen
if not exist public\build mkdir public\build
if not exist public\images mkdir public\images

:: Copiar los archivos compilados
xcopy /E /I /Y ..\public\build public\build
if exist ..\public\images\logo-icon.svg copy ..\public\images\logo-icon.svg public\images\logo-icon.svg

:: Crear el ZIP con la herramienta nativa de Windows
echo Creando el archivo ZIP...
powershell -Command "Compress-Archive -Path * -DestinationPath '..\%DEPLOY_ZIP%' -Force"

:: Volver al directorio original y limpiar
cd ..
rd /s /q temp

if exist "%DEPLOY_ZIP%" (
    echo.
    echo El archivo %DEPLOY_ZIP% ha sido creado correctamente.
    echo.
    echo IMPORTANTE: 
    echo - El ZIP contiene solo los archivos necesarios para producción
    echo - Las carpetas node_modules, vendor, .git, etc. no están incluidas
    echo.
    echo Ahora puedes subir este archivo al servidor y extraerlo.
) else (
    echo.
    echo ERROR: No se pudo crear el archivo ZIP.
    echo Por favor intenta generar el ZIP manualmente.
)
echo.
pause

