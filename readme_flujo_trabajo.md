# Flujo de Trabajo del Proyecto Andes

Este documento describe el flujo de trabajo completo para el desarrollo y despliegue del proyecto Andes.

## Arquitectura del Flujo de Trabajo

```
Máquina Local (Desarrollo)
    ↓ git push
GitHub (Repositorio Central)
    ↓ git pull
Servidor Web (192.168.100.75)
    ↓ Apache
Navegador Web
```

## Estado Actual

### ✅ Completado

1. **Conexión SSH sin contraseña**
   - Máquina local ↔ Servidor web (192.168.100.75)
   - Clave: `~/.ssh/id_rsa`

2. **Servidor LAMP instalado**
   - Apache 2.4.58
   - MariaDB 10.11.14
   - PHP 8.3.6
   - Firewall UFW configurado

3. **Repositorio Git local**
   - Directorio: `/home/edo/Proyectos CODE/Andes`
   - Rama: main
   - Remoto: git@github.com:edojara/Andes.git

4. **Clave SSH para GitHub (Máquina Local)**
   - Archivo: `~/.ssh/id_ed25519_github`
   - Configuración SSH: ✅ Configurada
   - Estado: ✅ Conectado y funcionando
   - Push a GitHub: ✅ Exitoso

5. **Git instalado en servidor web**
   - Versión: 2.43.0
   - Estado: ✅ Instalado

6. **Clave SSH para GitHub (Servidor Web)**
   - Archivo: `~/.ssh/id_ed25519_github_server`
   - Configuración SSH: ✅ Configurada
   - Estado: ⏳ Esperando agregar a GitHub

### ⏳ Pendiente

1. **Agregar clave SSH del servidor a GitHub**
   - Clave pública del servidor: `ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIACeJifr8GQZaKWmZkmyb0ofB+IdHc/J5BzcVSo6fYHW edo@192.168.100.75`
   - Acción requerida: Agregar a GitHub

2. **Clonar repositorio en servidor web**
   - Directorio: `/var/www/html/andes`
   - Acción requerida: Después de agregar clave a GitHub

## Pasos para Completar la Configuración

### Paso 1: Agregar Clave SSH del Servidor a GitHub

1. Ve a [GitHub Settings > SSH and GPG keys](https://github.com/settings/keys)
2. Haz clic en "New SSH key"
3. En "Title", ingresa: `Andes Project - Web Server (192.168.100.75)`
4. En "Key type", selecciona: `Authentication Key`
5. En "Key", pega esta clave:

```
ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIACeJifr8GQZaKWmZkmyb0ofB+IdHc/J5BzcVSo6fYHW edo@192.168.100.75
```

6. Haz clic en "Add SSH key"

### Paso 2: Verificar Conexión desde el Servidor

Desde tu máquina local, ejecuta:

```bash
ssh edo@192.168.100.75 "ssh -T git@github.com"
```

Deberías ver:
```
Hi edojara! You've successfully authenticated, but GitHub does not provide shell access.
```

### Paso 3: Clonar Repositorio en el Servidor

Desde tu máquina local, ejecuta:

```bash
ssh edo@192.168.100.75 "cd /tmp && git clone git@github.com:edojara/Andes.git andes && echo '123' | sudo -S mv /tmp/andes /var/www/html/ && echo '123' | sudo -S chown -R www-data:www-data /var/www/html/andes && echo '123' | sudo -S chmod -R 755 /var/www/html/andes"
```

### Paso 4: Configurar Apache para el Nuevo Directorio

Desde tu máquina local, ejecuta:

```bash
ssh edo@192.168.100.75 "echo '123' | sudo -S tee /etc/apache2/sites-available/andes.conf > /dev/null << 'EOF'
<VirtualHost *:80>
    ServerName 192.168.100.75
    DocumentRoot /var/www/html/andes

    <Directory /var/www/html/andes>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/andes_error.log
    CustomLog \${APACHE_LOG_DIR}/andes_access.log combined
</VirtualHost>
EOF
"

Habilita el sitio:

```bash
ssh edo@192.168.100.75 "echo '123' | sudo -S a2ensite andes.conf && echo '123' | sudo -S systemctl reload apache2"
```

### Paso 5: Verificar el Despliegue

Desde tu navegador, accede a:
- `http://192.168.100.75/`

Deberías ver la página inicial del proyecto Andes.

## Flujo de Trabajo Diario

### 1. Desarrollar en la Máquina Local

Crea o modifica archivos en el directorio del proyecto:
```bash
cd /home/edo/Proyectos\ CODE/Andes
```

### 2. Verificar Cambios

```bash
git status
```

### 3. Agregar Archivos al Área de Staging

```bash
# Agregar todos los archivos
git add .

# O agregar archivos específicos
git add archivo1.php archivo2.html
```

### 4. Hacer Commit

```bash
git commit -m "Descripción de los cambios"
```

### 5. Enviar a GitHub

```bash
git push origin main
```

### 6. Actualizar el Servidor Web

Desde tu máquina local, ejecuta:

```bash
ssh edo@192.168.100.75 "cd /var/www/html/andes && git pull origin main"
```

O crea un script de actualización en el servidor:

```bash
ssh edo@192.168.100.75 "echo '123' | sudo -S tee /var/www/html/update.sh > /dev/null << 'EOF'
#!/bin/bash
cd /var/www/html/andes
git pull origin main
echo 'Actualizado: $(date)'
EOF
" && ssh edo@192.168.100.75 "echo '123' | sudo -S chmod +x /var/www/html/update.sh"
```

Luego, para actualizar el servidor:

```bash
ssh edo@192.168.100.75 "/var/www/html/update.sh"
```

## Comandos Rápidos

### Desde la Máquina Local

```bash
# Ver estado
git status

# Agregar y commit
git add .
git commit -m "Mensaje"

# Push a GitHub
git push

# Actualizar servidor
ssh edo@192.168.100.75 "cd /var/www/html/andes && git pull"
```

### Desde el Servidor Web

```bash
# Actualizar desde GitHub
cd /var/www/html/andes
git pull origin main

# Ver estado
git status

# Ver historial
git log --oneline
```

## Estructura de Archivos del Proyecto

```
Andes/
├── .git/                  # Repositorio Git
├── .gitignore             # Archivos ignorados por Git
├── index.html             # Página inicial
├── readme_ssh.md          # Documentación SSH
├── readme_lamp.md         # Documentación LAMP
├── readme_github.md       # Documentación GitHub
└── readme_flujo_trabajo.md  # Este archivo
```

## Resumen de Claves SSH

### Máquina Local

| Propósito | Archivo | Estado |
|-----------|----------|--------|
| Servidor Web | `~/.ssh/id_rsa` | ✅ Activo |
| GitHub | `~/.ssh/id_ed25519_github` | ✅ Activo |

### Servidor Web (192.168.100.75)

| Propósito | Archivo | Estado |
|-----------|----------|--------|
| GitHub | `~/.ssh/id_ed25519_github_server` | ⏳ Pendiente agregar a GitHub |

## URLs Importantes

- **Repositorio GitHub:** https://github.com/edojara/Andes
- **Servidor Web:** http://192.168.100.75/
- **Servidor Web (Andes):** http://192.168.100.75/andes/ (después de configurar)
- **Configuración SSH GitHub:** https://github.com/settings/keys

## Solución de Problemas

### Error: Permission denied (publickey) en el servidor

- Verifica que la clave SSH del servidor esté agregada a GitHub
- Verifica la configuración SSH: `cat ~/.ssh/config`
- Prueba la conexión: `ssh -T git@github.com`

### Error: Updates were rejected en git pull

```bash
git pull origin main --rebase
```

### Error: Apache no muestra el nuevo sitio

```bash
# Verificar configuración
sudo apache2ctl configtest

# Recargar Apache
sudo systemctl reload apache2

# Verificar logs
sudo tail -f /var/log/apache2/error.log
```

### Error: Permisos en archivos del servidor

```bash
# Corregir permisos
sudo chown -R www-data:www-data /var/www/html/andes
sudo chmod -R 755 /var/www/html/andes
```

## Próximos Pasos

1. ✅ Agregar clave SSH del servidor a GitHub
2. ✅ Clonar repositorio en servidor web
3. ✅ Configurar Apache para el nuevo directorio
4. ⏳ Crear estructura del proyecto (PHP, HTML, CSS, JS)
5. ⏳ Configurar base de datos en el servidor
6. ⏳ Implementar funcionalidades del proyecto

---

**Última actualización:** 2026-02-01  
**Estado:** ⏳ Esperando agregar clave SSH del servidor a GitHub