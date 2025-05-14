#!/bin/bash
echo "Generando rutas Ziggy..."
/mnt/c/xampp/php/php.exe artisan ziggy:generate

echo "Compilando para producción..."
npm run build 

echo "Verificando archivos generados..."
ls -la /mnt/c/xampp/htdocs/censopc/solutions/public/build/

# Generar timestamp para evitar conflictos
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
DEPLOY_ZIP="naturalbio-deploy-${TIMESTAMP}.zip"

echo "Preparando archivos para despliegue..."
cd /mnt/c/xampp/htdocs/censopc/solutions

# Crear directorio temporal para exclusiones
mkdir -p temp

# Crear archivo de exclusiones
cat > temp/exclude-list.txt << EOF
node_modules/**
.git/**
.github/**
.vscode/**
tests/**
vendor/**
*.git*
*.log
*.zip
temp/**
EOF

echo "Creando ZIP completo del proyecto: ${DEPLOY_ZIP}"

# Verificar si zip está disponible
if command -v zip &> /dev/null; then
    zip -r "${DEPLOY_ZIP}" . -x@temp/exclude-list.txt
    ZIP_STATUS=$?
else
    echo "Comando zip no encontrado. Intentando con tar+gzip..."
    
    # Crear lista de exclusiones para tar
    tar --exclude-from=temp/exclude-list.txt -czf "${DEPLOY_ZIP}" .
    ZIP_STATUS=$?
fi

# Limpiar archivos temporales
rm -rf temp

if [ $ZIP_STATUS -eq 0 ] && [ -f "${DEPLOY_ZIP}" ]; then
    echo "Archivo ${DEPLOY_ZIP} generado con éxito."
    echo ""
    echo "IMPORTANTE:"
    echo "- Asegúrate de que el directorio public/build/ está incluido"
    echo "- Asegúrate de que el archivo public/images/logo-icon.svg está incluido"
    echo ""
    echo "¡Listo para desplegar! Suba el archivo ${DEPLOY_ZIP} a su instancia AWS."
else
    echo "ERROR: No se pudo crear el archivo ZIP."
    echo "Intenta crear el archivo ZIP manualmente excluyendo node_modules, .git, vendor, etc."
fi

