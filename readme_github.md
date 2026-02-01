# Configuración de GitHub con SSH

Este documento explica cómo configurar la sincronización con GitHub usando claves SSH.

## Clave SSH Generada

Se ha generado una clave SSH específica para GitHub:

- **Tipo:** ED25519 (más seguro que RSA)
- **Archivo privado:** `~/.ssh/id_ed25519_github`
- **Archivo público:** `~/.ssh/id_ed25519_github.pub`
- **Comentario:** edojara@github.com

## Clave Pública

```
ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIHAKrYGmPgX9bHxOb2PTAH9OeyI1pgBp6EBlWZQogday edojara@github.com
```

## Pasos para Agregar la Clave a GitHub

### 1. Copiar la clave pública

La clave pública ya está mostrada arriba. Copia el contenido completo.

### 2. Agregar la clave a GitHub

1. Ve a [GitHub Settings > SSH and GPG keys](https://github.com/settings/keys)
2. Haz clic en "New SSH key"
3. En "Title", ingresa: `Andes Project - Local Machine`
4. En "Key type", selecciona: `Authentication Key`
5. En "Key", pega la clave pública copiada
6. Haz clic en "Add SSH key"

### 3. Verificar la conexión

Una vez agregada la clave, verifica la conexión:

```bash
ssh -T git@github.com
```

Deberías ver un mensaje como:
```
Hi edojara! You've successfully authenticated, but GitHub does not provide shell access.
```

## Configuración de SSH

Se ha configurado el archivo `~/.ssh/config` para usar automáticamente la clave de GitHub:

```
Host github.com
    HostName github.com
    User git
    IdentityFile ~/.ssh/id_ed25519_github
    IdentitiesOnly yes
```

## Repositorio Git

El repositorio local ya está inicializado y configurado:

- **Directorio:** `/home/edo/Proyectos CODE/Andes`
- **Rama:** main
- **Remoto:** git@github.com:edojara/Andes.git

## Flujo de Trabajo

### 1. Desarrollar en la máquina local

Crea o modifica archivos en el directorio del proyecto.

### 2. Verificar cambios

```bash
git status
```

### 3. Agregar archivos al área de staging

```bash
# Agregar todos los archivos
git add .

# O agregar archivos específicos
git add archivo1.php archivo2.html
```

### 4. Hacer commit

```bash
git commit -m "Descripción de los cambios"
```

### 5. Enviar cambios a GitHub

```bash
git push origin main
```

### 6. Actualizar servidor web

En el servidor web (192.168.100.75):

```bash
# Clonar o actualizar el repositorio
cd /var/www/html
git clone git@github.com:edojara/Andes.git andes
# O si ya existe:
cd andes
git pull origin main
```

## Comandos Útiles

### Ver el historial de commits
```bash
git log --oneline
```

### Ver ramas
```bash
git branch -a
```

### Crear una nueva rama
```bash
git checkout -b nombre-rama
```

### Cambiar de rama
```bash
git checkout nombre-rama
```

### Fusionar ramas
```bash
git checkout main
git merge nombre-rama
```

### Ver cambios en un archivo
```bash
git diff archivo.php
```

### Deshacer cambios no commitados
```bash
git checkout -- archivo.php
```

### Deshacer el último commit
```bash
git reset --soft HEAD~1
```

## Configuración del Servidor Web

### Instalar Git en el servidor

```bash
ssh edo@192.168.100.75
sudo apt install -y git
```

### Configurar SSH del servidor para GitHub

En el servidor web, genera una clave SSH para GitHub:

```bash
ssh-keygen -t ed25519 -C "edo@192.168.100.75" -f ~/.ssh/id_ed25519_github_server -N ""
```

Agrega la configuración al archivo `~/.ssh/config` del servidor:

```
Host github.com
    HostName github.com
    User git
    IdentityFile ~/.ssh/id_ed25519_github_server
    IdentitiesOnly yes
```

Muestra la clave pública del servidor:

```bash
cat ~/.ssh/id_ed25519_github_server.pub
```

Agrega esta clave a GitHub con el título: `Andes Project - Web Server`

### Clonar el repositorio en el servidor

```bash
cd /var/www/html
git clone git@github.com:edojara/Andes.git andes
sudo chown -R www-data:www-data andes
sudo chmod -R 755 andes
```

### Configurar Apache para usar el repositorio

Edita la configuración de Apache:

```bash
sudo nano /etc/apache2/sites-available/andes.conf
```

Agrega:

```apache
<VirtualHost *:80>
    ServerName 192.168.100.75
    DocumentRoot /var/www/html/andes

    <Directory /var/www/html/andes>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/andes_error.log
    CustomLog ${APACHE_LOG_DIR}/andes_access.log combined
</VirtualHost>
```

Habilita el sitio:

```bash
sudo a2ensite andes.conf
sudo systemctl reload apache2
```

## Automatización de Despliegue

### Script de actualización en el servidor

Crea el archivo `/var/www/html/update.sh`:

```bash
#!/bin/bash
cd /var/www/html/andes
git pull origin main
sudo systemctl reload apache2
echo "Actualizado: $(date)"
```

Hazlo ejecutable:

```bash
sudo chmod +x /var/www/html/update.sh
```

Para actualizar el servidor desde tu máquina local:

```bash
ssh edo@192.168.100.75 "/var/www/html/update.sh"
```

### Webhook de GitHub (Opcional)

Para actualizaciones automáticas cuando haces push a GitHub, puedes configurar un webhook que llame a un script en tu servidor.

## Solución de Problemas

### Error: Permission denied (publickey)

- Verifica que la clave SSH esté agregada a GitHub
- Verifica que la configuración de SSH sea correcta
- Prueba la conexión: `ssh -T git@github.com`

### Error: Host key verification failed

Agrega la clave de host de GitHub:

```bash
ssh-keyscan github.com >> ~/.ssh/known_hosts
```

### Error: fatal: remote origin already exists

Elimina el remoto existente:

```bash
git remote remove origin
git remote add origin git@github.com:edojara/Andes.git
```

### Error: Updates were rejected

Primero haz pull de los cambios remotos:

```bash
git pull origin main --rebase
```

Luego haz push:

```bash
git push origin main
```

## Recursos Adicionales

- [Documentación de Git](https://git-scm.com/doc)
- [Documentación de GitHub](https://docs.github.com/)
- [Guía de SSH de GitHub](https://docs.github.com/en/authentication/connecting-to-github-with-ssh)

---

**Última actualización:** 2026-02-01  
**Estado:** ⏳ Esperando agregar clave SSH a GitHub