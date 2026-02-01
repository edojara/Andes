# Configuración de Conexión SSH sin Contraseña

Este documento explica cómo configurar una conexión SSH sin contraseña entre tu máquina local y el servidor web.

## Información de la Conexión

- **Servidor:** 192.168.100.75
- **Usuario:** edo
- **Hostname:** wordpress
- **Estado:** ✅ Configurado y funcionando

## Paso 1: Generar Claves SSH en la Máquina Local

Si no tienes claves SSH generadas, ejecuta este comando en tu máquina local:

```bash
ssh-keygen -t rsa -b 4096 -f ~/.ssh/id_rsa -N ""
```

Esto creará:
- Clave privada: `~/.ssh/id_rsa`
- Clave pública: `~/.ssh/id_rsa.pub`

## Paso 2: Copiar la Clave Pública al Servidor Remoto

### Opción A: Usando ssh-copy-id (método recomendado)

```bash
ssh-copy-id edo@192.168.100.75
```

Te pedirá la contraseña del usuario en el servidor una sola vez.

### Opción B: Método Manual

Si `ssh-copy-id` no está disponible, sigue estos pasos:

1. Muestra tu clave pública local:
   ```bash
   cat ~/.ssh/id_rsa.pub
   ```

2. Conéctate al servidor:
   ```bash
   ssh edo@192.168.100.75
   ```

3. En el servidor, crea el directorio `.ssh` si no existe:
   ```bash
   mkdir -p ~/.ssh
   chmod 700 ~/.ssh
   ```

4. Agrega tu clave pública al archivo `authorized_keys`:
   ```bash
   nano ~/.ssh/authorized_keys
   ```

5. Pega tu clave pública al final del archivo y guarda (Ctrl+O, Enter, Ctrl+X)

6. Establece los permisos correctos:
   ```bash
   chmod 600 ~/.ssh/authorized_keys
   ```

## Paso 3: Probar la Conexión

Desde tu máquina local, prueba conectarte sin contraseña:

```bash
ssh edo@192.168.100.75
```

Si todo está configurado correctamente, deberías conectarte sin que te pida contraseña.

## Verificación

Para verificar que la conexión funciona correctamente, ejecuta:

```bash
ssh edo@192.168.100.75 "echo 'Conexión SSH exitosa' && hostname && whoami"
```

Deberías ver algo como:
```
Conexión SSH exitosa
wordpress
edo
```

## Solución de Problemas

### Si te pide contraseña después de configurar:

1. Verifica los permisos en el servidor:
   ```bash
   ls -la ~/.ssh/
   ```
   Deberías ver:
   - `authorized_keys` con permisos 600
   - Directorio `.ssh` con permisos 700

2. Verifica que tu clave pública esté en `authorized_keys`:
   ```bash
   cat ~/.ssh/authorized_keys
   ```

3. Verifica la configuración de SSH en el servidor:
   ```bash
   sudo cat /etc/ssh/sshd_config | grep PubkeyAuthentication
   ```
   Debe decir: `PubkeyAuthentication yes`

### Si el servidor rechaza la conexión:

1. Verifica que el servicio SSH esté corriendo en el servidor:
   ```bash
   sudo systemctl status ssh
   ```

2. Verifica que el firewall permita conexiones SSH:
   ```bash
   sudo ufw status
   ```

## Uso Diario

Una vez configurado, puedes conectarte simplemente con:

```bash
ssh edo@192.168.100.75
```

O ejecutar comandos directamente en el servidor:

```bash
ssh edo@192.168.100.75 "ls -la"
```

---

**Última actualización:** 2026-02-01  
**Estado:** ✅ Conexión configurada y funcionando  
**Probado en:** Ubuntu Server (192.168.100.75) → Ubuntu Local