#!/bin/bash
# Script de actualización del proyecto Andes en el servidor web
# Este script actualiza el repositorio desde GitHub

echo "=========================================="
echo "Actualizando repositorio desde GitHub..."
echo "=========================================="

# Cambiar al directorio del proyecto
cd /var/www/html/andes

# Verificar si se ejecuta con sudo
if [ "$EUID" -ne 0 ]; then
    echo "Este script debe ejecutarse con sudo"
    echo "Ejemplo: sudo /var/www/html/andes/update.sh"
    exit 1
fi

# Cambiar propietario temporalmente para poder hacer git pull
chown -R edo:edo /var/www/html/andes

# Actualizar desde GitHub
su - edo -c "cd /var/www/html/andes && git pull origin main"

# Restaurar propietario correcto
chown -R www-data:www-data /var/www/html/andes

echo ""
echo "=========================================="
echo "Repositorio actualizado: $(date)"
echo "=========================================="

echo ""
echo "Archivos en el servidor:"
ls -la

echo ""
echo "=========================================="
echo "Actualización completada exitosamente"
echo "=========================================="