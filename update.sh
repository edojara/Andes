#!/bin/bash
# Script de actualización del proyecto Andes en el servidor web
# Este script actualiza el repositorio desde GitHub

cd /var/www/html/andes

echo "=========================================="
echo "Actualizando repositorio desde GitHub..."
echo "=========================================="

# Actualizar desde GitHub
git pull origin main

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