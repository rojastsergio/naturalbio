# Script para crear el archivo de despliegue
Write-Host "Creando archivo de despliegue para naturalbio..."

# Generar timestamp para el nombre del archivo
$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$zipFileName = "naturalbio-deploy-$timestamp.zip"

# Comprimir los archivos necesarios para el servidor
Compress-Archive -Path `
  "app",`
  "bootstrap",`
  "config",`
  "database/factories",`
  "database/migrations",`
  "database/seeders",`
  "Modules/Appointments",`
  "Modules/Doctors",`
  "Modules/Patients",`
  "Modules/Prescriptions",`
  "Modules/Recommendations",`
  "Modules/Supplements",`
  "Modules/Therapies",`
  "public/index.php",`
  "public/.htaccess",`
  "public/favicon.ico",`
  "public/robots.txt",`
  "public/build",`
  "public/css",`
  "public/js",`
  "public/images",`
  "public/fonts",`
  "public/img/logo-icon.svg",`
  "resources/views",`
  "resources/css",`
  "resources/js/Components",`
  "resources/js/Layouts",`
  "resources/js/Pages",`
  "resources/js/app.js",`
  "resources/js/bootstrap.js",`
  "resources/js/ziggy.js",`
  "routes",`
  "storage/app",`
  "storage/framework",`
  "storage/logs",`
  "artisan",`
  "composer.json",`
  "composer.lock",`
  ".env.example",`
  "package.json",`
  "vite.config.js",`
  "tailwind.config.js"`
  -DestinationPath $zipFileName -Force

# Verificar si el archivo se creó con éxito
if (Test-Path -Path $zipFileName -PathType Leaf) {
    Write-Host "El archivo $zipFileName se ha creado correctamente" -ForegroundColor Green
    Write-Host "Tamaño: $((Get-Item $zipFileName).Length / 1MB) MB" -ForegroundColor Green
    
    Write-Host ""
    Write-Host "INSTRUCCIONES PARA SUBIR AL SERVIDOR:" -ForegroundColor Yellow
    Write-Host "1. Sube el archivo $zipFileName a tu servidor"
    Write-Host "2. Extrae el archivo en el directorio apropiado"
    Write-Host "3. Asegúrate de que composer y npm estén instalados"
    Write-Host "4. Ejecuta: composer install --no-dev"
    Write-Host "5. Configura .env con tus variables de entorno"
    Write-Host "6. Ejecuta: php artisan migrate (si hay cambios en la base de datos)"
    Write-Host "7. Ejecuta: php artisan storage:link (si es la primera instalación)"
    Write-Host "8. Configura los permisos adecuados para storage y bootstrap/cache"
} else {
    Write-Host "Error al crear el archivo zip" -ForegroundColor Red
}

Write-Host ""
Write-Host "Presiona cualquier tecla para continuar..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")