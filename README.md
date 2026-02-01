# Proyecto Andes

Este proyecto implementa un servidor web LAMP con flujo de trabajo automatizado usando Git y GitHub.

## 🚀 Estado del Proyecto

### ✅ Configuración Completada

1. **Conexión SSH sin Contraseña**
   - Máquina local ↔ Servidor web (192.168.100.75)
   - Clave: `~/.ssh/id_rsa`
   - Documentación: [`readme_ssh.md`](readme_ssh.md)

2. **Servidor LAMP Instalado**
   - Apache 2.4.58 ✅
   - MariaDB 10.11.14 ✅
   - PHP 8.3.6 ✅
   - Firewall UFW configurado ✅
   - Documentación: [`readme_lamp.md`](readme_lamp.md)

3. **Repositorio Git Local**
   - Directorio: `/home/edo/Proyectos CODE/Andes`
   - Rama: main
   - Remoto: git@github.com:edojara/Andes.git
   - Estado: ✅ Conectado y funcionando

4. **Clave SSH para GitHub (Máquina Local)**
   - Archivo: `~/.ssh/id_ed25519_github`
   - Configuración SSH: ✅ Configurada
   - Estado: ✅ Conectado y funcionando
   - Push a GitHub: ✅ Exitoso
   - Documentación: [`readme_github.md`](readme_github.md)

5. **Git en Servidor Web**
   - Versión: 2.43.0 ✅
   - Clave SSH: `~/.ssh/id_ed25519_github_server`
   - Configuración SSH: ✅ Configurada
   - Estado: ✅ Conectado y funcionando
   - Repositorio clonado: ✅ `/var/www/html/andes`

6. **Script de Actualización**
   - Archivo: [`update.sh`](update.sh)
   - Estado: ✅ Funcionando correctamente
   - Uso: `sudo bash /var/www/html/andes/update.sh`

## 🔄 Flujo de Trabajo

```
Máquina Local (Desarrollo)
    ↓ git push
GitHub (Repositorio Central)
    ↓ git pull
Servidor Web (192.168.100.75)
    ↓ Apache
Navegador Web
```

## 📝 Comandos Diarios

### Desde la Máquina Local

```bash
# Ver cambios
git status

# Agregar y commit
git add .
git commit -m "Mensaje"

# Push a GitHub
git push

# Actualizar servidor
ssh edo@192.168.100.75 "echo '123' | sudo -S bash /var/www/html/andes/update.sh"
```

### Desde el Servidor Web

```bash
# Actualizar desde GitHub
sudo bash /var/www/html/andes/update.sh

# O manualmente
cd /var/www/html/andes
git pull origin main
```

## 📁 Estructura del Proyecto

```
Andes/
├── .git/                      # Repositorio Git
├── .gitignore                 # Archivos ignorados por Git
├── andes.conf                 # Configuración de Apache
├── index.html                 # Página inicial
├── update.sh                  # Script de actualización
├── README.md                  # Este archivo
├── readme_ssh.md              # Documentación SSH
├── readme_lamp.md             # Documentación LAMP
├── readme_github.md           # Documentación GitHub
└── readme_flujo_trabajo.md   # Flujo de trabajo
```

## 🌐 URLs Importantes

- **Repositorio GitHub:** https://github.com/edojara/Andes
- **Servidor Web:** http://192.168.100.75/
- **Servidor Web (Andes):** http://192.168.100.75/andes/
- **Configuración SSH GitHub:** https://github.com/settings/keys

## 🔧 Configuración de Apache

El archivo [`andes.conf`](andes.conf) contiene la configuración de Apache para el proyecto. Para habilitarlo en el servidor:

```bash
sudo cp /var/www/html/andes/andes.conf /etc/apache2/sites-available/
sudo a2ensite andes.conf
sudo systemctl reload apache2
```

## 📊 Información del Servidor

- **IP:** 192.168.100.75
- **Hostname:** wordpress
- **Sistema:** Ubuntu 24.04.3 LTS
- **Usuario:** edo
- **Contraseña sudo:** 123

## 🗄️ Base de Datos

- **Motor:** MariaDB 10.11.14
- **Base de datos de prueba:** `lamp_test`
- **Usuario:** `lamp_user`
- **Contraseña:** `lamp_password`

## 📚 Documentación

- [`readme_ssh.md`](readme_ssh.md) - Configuración de conexión SSH
- [`readme_lamp.md`](readme_lamp.md) - Documentación del servidor LAMP
- [`readme_github.md`](readme_github.md) - Configuración de GitHub con SSH
- [`readme_flujo_trabajo.md`](readme_flujo_trabajo.md) - Flujo de trabajo completo

## 🚀 Comenzar a Desarrollar

1. Crea o modifica archivos en el directorio del proyecto
2. Agrega los cambios: `git add .`
3. Haz commit: `git commit -m "Mensaje"`
4. Envía a GitHub: `git push`
5. Actualiza el servidor: `ssh edo@192.168.100.75 "echo '123' | sudo -S bash /var/www/html/andes/update.sh"`

## 🔒 Seguridad

- ✅ SSH sin contraseña configurado
- ✅ Firewall UFW activo
- ✅ Claves SSH separadas para diferentes propósitos
- ⚠️ Se recomienda cambiar la contraseña de sudo
- ⚠️ Se recomienda configurar autenticación SSH solo con claves

## 📞 Soporte

Para problemas o preguntas, consulta la documentación específica:
- SSH: [`readme_ssh.md`](readme_ssh.md)
- LAMP: [`readme_lamp.md`](readme_lamp.md)
- GitHub: [`readme_github.md`](readme_github.md)
- Flujo de trabajo: [`readme_flujo_trabajo.md`](readme_flujo_trabajo.md)

---

**Última actualización:** 2026-02-01  
**Estado:** ✅ Configuración completada y funcionando  
**Versión:** 1.0.0