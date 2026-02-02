#!/bin/bash
# Script para crear la base de datos del Club Deportivo Andes

echo "Creando base de datos..."

# Ejecutar el script SQL
mysql -u root -p123 < /var/www/andes/database/setup.sql

if [ $? -eq 0 ]; then
    echo "Base de datos creada exitosamente!"
else
    echo "Error al crear la base de datos"
    exit 1
fi
