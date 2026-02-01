# Arquitectura del Sitio Web del Club Deportivo

Este documento describe la arquitectura completa del sistema web para el club deportivo.

## 📋 Requerimientos del Sistema

### Funcionalidades Principales

1. **Gestión de Partidos**
   - Mostrar información del próximo partido
   - Historial de partidos ya realizados
   - Resultados de partidos
   - Estadísticas del equipo

2. **Gestión de Jugadores**
   - Perfiles de jugadores
   - Información personal (nombre, posición, número, etc.)
   - Foto de perfil de cada jugador
   - Estadísticas individuales

3. **Galería de Fotos**
   - Fotos de las distintas jornadas/partidos
   - Organización por fecha o evento
   - Visualización en galería

4. **Información del Club**
   - Sobre el club
   - Historia
   - Contacto
   - Ubicación

## 🏗️ Arquitectura del Sistema

```
┌─────────────────────────────────────────────────────────────┐
│                    Navegador Web                      │
│              (HTML/CSS/JavaScript)                    │
└────────────────────┬──────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                   Frontend (PHP)                      │
│  - Páginas dinámicas                                 │
│  - Templates HTML                                     │
└────────────────────┬──────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│              API REST (PHP)                            │
│  - Endpoints para partidos                              │
│  - Endpoints para jugadores                               │
│  - Endpoints para fotos                                 │
│  - Endpoints para club                                   │
└────────────────────┬──────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│           Base de Datos (MariaDB)                      │
│  - Tabla: partidos                                     │
│  - Tabla: jugadores                                     │
│  - Tabla: fotos                                        │
│  - Tabla: club                                         │
└─────────────────────────────────────────────────────────────┘
```

## 🗄️ Diseño de la Base de Datos

### Tabla: club

```sql
CREATE TABLE club (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    historia TEXT,
    direccion VARCHAR(255),
    telefono VARCHAR(20),
    email VARCHAR(100),
    fundado_en DATE,
    logo_url VARCHAR(255),
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Tabla: jugadores

```sql
CREATE TABLE jugadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    numero_camiseta INT,
    posicion VARCHAR(50),
    fecha_nacimiento DATE,
    altura DECIMAL(5,2),
    peso DECIMAL(5,2),
    foto_url VARCHAR(255),
    biografia TEXT,
    estadisticas JSON,
    activo BOOLEAN DEFAULT TRUE,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Tabla: partidos

```sql
CREATE TABLE partidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rival VARCHAR(100) NOT NULL,
    fecha DATE NOT NULL,
    hora TIME,
    lugar VARCHAR(255),
    resultado_local INT,
    resultado_visitante INT,
    es_local BOOLEAN DEFAULT TRUE,
    jornada INT,
    temporada VARCHAR(20),
    observaciones TEXT,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Tabla: partidos_jugadores

```sql
CREATE TABLE partidos_jugadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partido_id INT NOT NULL,
    jugador_id INT NOT NULL,
    minutos_jugados INT DEFAULT 0,
    goles INT DEFAULT 0,
    asistencias INT DEFAULT 0,
    tarjetas_amarillas INT DEFAULT 0,
    tarjetas_rojas INT DEFAULT 0,
    FOREIGN KEY (partido_id) REFERENCES partidos(id) ON DELETE CASCADE,
    FOREIGN KEY (jugador_id) REFERENCES jugadores(id) ON DELETE CASCADE
);
```

### Tabla: fotos

```sql
CREATE TABLE fotos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200),
    descripcion TEXT,
    partido_id INT,
    fecha_evento DATE,
    ruta_archivo VARCHAR(255) NOT NULL,
    tipo VARCHAR(50) DEFAULT 'partido',
    orden INT DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (partido_id) REFERENCES partidos(id) ON DELETE SET NULL
);
```

## 🔌 Estructura del Frontend

```
Andes/
├── index.php              # Página principal
├── pages/
│   ├── about.php          # Sobre el club
│   ├── events.php         # Eventos y partidos
│   ├── players.php        # Jugadores
│   ├── gallery.php        # Galería de fotos
│   └── contact.php        # Contacto
├── api/
│   ├── partidos.php       # API de partidos
│   ├── jugadores.php      # API de jugadores
│   ├── fotos.php         # API de fotos
│   └── club.php          # API del club
├── assets/
│   ├── css/
│   │   └── styles.css   # Estilos principales
│   ├── js/
│   │   └── main.js      # JavaScript principal
│   └── images/
│       └── uploads/      # Fotos subidas
├── includes/
│   ├── db.php            # Conexión a BD
│   ├── header.php        # Header común
│   └── footer.php        # Footer común
└── uploads/                # Directorio de uploads
```

## 🔌 API REST Endpoints

### Partidos

```
GET    /api/partidos              # Listar todos los partidos
GET    /api/partidos/:id          # Obtener un partido específico
GET    /api/partidos/proximo      # Obtener próximo partido
GET    /api/partidos/historial   # Obtener historial
POST   /api/partidos              # Crear nuevo partido
PUT    /api/partidos/:id          # Actualizar partido
DELETE /api/partidos/:id          # Eliminar partido
```

### Jugadores

```
GET    /api/jugadores             # Listar todos los jugadores
GET    /api/jugadores/:id         # Obtener un jugador específico
GET    /api/jugadores/activos    # Obtener jugadores activos
POST   /api/jugadores             # Crear nuevo jugador
PUT    /api/jugadores/:id         # Actualizar jugador
DELETE /api/jugadores/:id         # Eliminar jugador
```

### Fotos

```
GET    /api/fotos                 # Listar todas las fotos
GET    /api/fotos/:id             # Obtener una foto específica
GET    /api/fotos/partido/:id    # Fotos de un partido
POST   /api/fotos                 # Subir nueva foto
DELETE /api/fotos/:id             # Eliminar foto
```

### Club

```
GET    /api/club                  # Obtener información del club
PUT    /api/club                  # Actualizar información del club
```

## 🎨 Diseño Visual

### Paleta de Colores

```css
--primary-color: #27F53F;       /* Verde/azul principal */
--secondary-color: #1a5c3a;    /* Verde oscuro secundario */
--accent-color: #2ecc71;        /* Verde acento */
--text-color: #000000;           /* Texto negro */
--bg-color: #ffffff;            /* Fondo blanco */
--success-color: #27ae60;       /* Verde éxito */
--danger-color: #e74c3c;        /* Rojo error */
--warning-color: #f39c12;      /* Amarilla advertencia */
--info-color: #3498db;         /* Azul información */
```

### Componentes Principales

1. **Header**
   - Logo del club
   - Navegación principal
   - Menú responsive

2. **Hero Section**
   - Imagen destacada
   - Título del próximo partido
   - Botón de acción

3. **Cards de Partidos**
   - Información del partido
   - Resultado
   - Fecha y hora
   - Lugar

4. **Cards de Jugadores**
   - Foto de perfil
   - Nombre y número
   - Posición
   - Estadísticas

5. **Galería de Fotos**
   - Grid de imágenes
   - Modal de visualización
   - Filtros por fecha

6. **Footer**
   - Información de contacto
   - Redes sociales
   - Copyright

## 🔐 Seguridad

### Autenticación

- Sistema de login para administradores
- Sesiones PHP
- Protección de rutas administrativas

### Validación

- Validación de formularios en frontend
- Sanitización de datos en backend
- Prepared statements para SQL

### Upload de Archivos

- Validación de tipos de archivo (jpg, png, webp)
- Límite de tamaño (5MB)
- Renombrado único de archivos
- Almacenamiento seguro

## 📊 Funcionalidades Específicas

### Próximo Partido

- Mostrar en página principal
- Countdown al partido
- Información completa (rival, fecha, hora, lugar)
- Mapa o dirección

### Historial de Partidos

- Lista cronológica
- Filtros por temporada
- Paginación
- Detalles de cada partido

### Perfiles de Jugadores

- Grid de tarjetas
- Búsqueda por nombre
- Filtros por posición
- Estadísticas detalladas

### Galería de Fotos

- Organización por evento/partido
- Lightbox para visualización
- Descarga de fotos
- Compartir en redes sociales

## 🚀 Plan de Implementación

### Fase 1: Base de Datos
- [ ] Crear base de datos
- [ ] Crear tablas
- [ ] Insertar datos de prueba

### Fase 2: Backend PHP
- [ ] Configurar conexión a BD
- [ ] Crear API de partidos
- [ ] Crear API de jugadores
- [ ] Crear API de fotos
- [ ] Crear API del club

### Fase 3: Frontend
- [ ] Crear estructura de directorios
- [ ] Crear header y footer
- [ ] Crear página principal
- [ ] Crear página de partidos
- [ ] Crear página de jugadores
- [ ] Crear página de galería
- [ ] Crear página de contacto

### Fase 4: Estilos y JavaScript
- [ ] Crear CSS principal
- [ ] Implementar diseño responsive
- [ ] Crear JavaScript para interactividad
- [ ] Implementar AJAX para API

### Fase 5: Funcionalidades Avanzadas
- [ ] Sistema de administración
- [ ] Upload de fotos
- [ ] Búsqueda y filtros
- [ ] Paginación

## 📝 Tecnologías

- **Backend:** PHP 8.3.6
- **Base de Datos:** MariaDB 10.11.14
- **Frontend:** HTML5, CSS3, JavaScript ES6+
- **Servidor Web:** Apache 2.4.58
- **Control de Versiones:** Git + GitHub

---

**Última actualización:** 2026-02-01  
**Estado:** 📋 Planificación completada