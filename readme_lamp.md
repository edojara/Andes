# Instalación del Servidor LAMP

Este documento describe la instalación y configuración del servidor LAMP (Linux, Apache, MySQL/MariaDB, PHP) en el servidor Ubuntu 24.04.

## Información del Servidor

- **IP del servidor:** 192.168.100.75
- **Hostname:** wordpress
- **Sistema operativo:** Ubuntu 24.04.3 LTS (Noble Numbat)
- **Usuario:** edo
- **Contraseña sudo:** 123

## Componentes Instalados

### 1. Apache Web Server
- **Versión:** Apache 2.4.58
- **Estado:** Activo y corriendo
- **Puerto:** 80 (HTTP), 443 (HTTPS)
- **Directorio raíz:** `/var/www/html`

### 2. MariaDB Database Server
- **Versión:** MariaDB 10.11.14
- **Estado:** Activo y corriendo
- **Puerto:** 3306
- **Base de datos de prueba:** `lamp_test`
- **Usuario de prueba:** `lamp_user`
- **Contraseña de prueba:** `lamp_password`

### 3. PHP
- **Versión:** PHP 8.3.6
- **Módulos instalados:**
  - php-mysql (conexión a MySQL)
  - php-curl (peticiones HTTP)
  - php-gd (manipulación de imágenes)
  - php-mbstring (soporte multibyte)
  - php-xml (manipulación XML)
  - php-zip (manipulación de archivos ZIP)
  - php-bcmath (cálculos matemáticos de precisión)
  - php-intl (internacionalización)
  - php-soap (protocolo SOAP)
  - php-imagick (manipulación avanzada de imágenes)

### 4. Firewall (UFW)
- **Estado:** Activo
- **Reglas configuradas:**
  - Apache Full (puertos 80 y 443)
  - OpenSSH (puerto 22)

## Pasos de Instalación

### Paso 1: Actualizar el Sistema

```bash
sudo apt update
sudo apt upgrade -y
```

### Paso 2: Instalar Apache

```bash
sudo apt install -y apache2 apache2-utils
sudo systemctl enable apache2
sudo systemctl start apache2
```

### Paso 3: Instalar MariaDB

```bash
sudo apt install -y mariadb-server mariadb-client
sudo systemctl enable mariadb
sudo systemctl start mariadb
```

### Paso 4: Instalar PHP y Módulos

```bash
sudo apt install -y php libapache2-mod-php php-mysql php-curl php-gd php-mbstring php-xml php-zip php-bcmath php-intl php-soap php-imagick
```

### Paso 5: Configurar Firewall

```bash
sudo apt install -y ufw
sudo ufw allow 'Apache Full'
sudo ufw allow OpenSSH
sudo ufw --force enable
```

### Paso 6: Crear Base de Datos y Usuario

```bash
sudo mysql -e "CREATE DATABASE IF NOT EXISTS lamp_test;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'lamp_user'@'localhost' IDENTIFIED BY 'lamp_password';"
sudo mysql -e "GRANT ALL PRIVILEGES ON lamp_test.* TO 'lamp_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"
```

## Verificación de la Instalación

### Verificar Apache

```bash
systemctl status apache2
curl http://localhost
```

### Verificar MariaDB

```bash
systemctl status mariadb
sudo mysql -e "SHOW DATABASES;"
```

### Verificar PHP

```bash
php -v
```

### Verificar Conexión PHP a MySQL

Crear archivo de prueba `/var/www/html/db_test.php`:

```php
<?php
$host = 'localhost';
$user = 'lamp_user';
$pass = 'lamp_password';
$db = 'lamp_test';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}
echo 'Conexión a MySQL exitosa!<br>';
echo 'Versión de MySQL: ' . $conn->server_info . '<br>';
echo 'Versión de PHP: ' . phpversion() . '<br>';

$result = $conn->query('SELECT VERSION()');
$row = $result->fetch_assoc();
echo 'Versión del servidor MySQL: ' . $row['VERSION()'] . '<br>';

$conn->close();
?>
```

Probar acceso:
```bash
curl http://localhost/db_test.php
```

## Archivos de Prueba

### info.php
Muestra información completa de PHP:
- URL: `http://192.168.100.75/info.php`
- Ubicación: `/var/www/html/info.php`

### db_test.php
Prueba la conexión a la base de datos:
- URL: `http://192.168.100.75/db_test.php`
- Ubicación: `/var/www/html/db_test.php`

## Comandos Útiles

### Reiniciar Apache
```bash
sudo systemctl restart apache2
```

### Reiniciar MariaDB
```bash
sudo systemctl restart mariadb
```

### Ver logs de Apache
```bash
sudo tail -f /var/log/apache2/error.log
sudo tail -f /var/log/apache2/access.log
```

### Ver logs de MariaDB
```bash
sudo tail -f /var/log/mysql/error.log
```

### Ver estado del firewall
```bash
sudo ufw status
```

## Acceso al Servidor

### Desde la máquina local
```bash
ssh edo@192.168.100.75
```

### Acceso a la base de datos
```bash
mysql -u lamp_user -p lamp_test
# Contraseña: lamp_password
```

## Seguridad Recomendada

1. **Cambiar la contraseña de root de MariaDB:**
   ```bash
   sudo mysql_secure_installation
   ```

2. **Cambiar la contraseña del usuario edo:**
   ```bash
   passwd
   ```

3. **Configurar autenticación SSH solo con claves:**
   Editar `/etc/ssh/sshd_config` y cambiar:
   ```
   PasswordAuthentication no
   ```

4. **Actualizar regularmente:**
   ```bash
   sudo apt update && sudo apt upgrade -y
   ```

## Solución de Problemas

### Apache no funciona
```bash
sudo systemctl status apache2
sudo apache2ctl configtest
```

### MariaDB no funciona
```bash
sudo systemctl status mariadb
sudo journalctl -u mariadb
```

### PHP no procesa archivos
```bash
sudo a2enmod php8.3
sudo systemctl restart apache2
```

### Error de conexión a la base de datos
```bash
# Verificar que el servicio esté corriendo
sudo systemctl status mariadb

# Verificar credenciales
mysql -u lamp_user -p lamp_test
```

## Recursos Adicionales

- [Documentación de Apache](https://httpd.apache.org/docs/)
- [Documentación de MariaDB](https://mariadb.com/kb/en/)
- [Documentación de PHP](https://www.php.net/docs.php)
- [Documentación de UFW](https://help.ubuntu.com/community/UFW)

---

**Última actualización:** 2026-02-01  
**Estado:** ✅ Servidor LAMP instalado y funcionando  
**Probado en:** Ubuntu Server 24.04.3 LTS (192.168.100.75)