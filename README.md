# 🚗 Chankas Car - Sistema de Gestión de Taller GNV

> **English version available**: [README.en.md](README.en.md)

<p align="center">
  <img src="public/favicon.png" alt="Chankas Car Logo" width="150">
</p>

<p align="center">
  Sistema integral de gestión para talleres de conversión a Gas Natural Vehicular (GNV)
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.1.33-777BB4?style=for-the-badge&logo=php" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-8.0.44-4479A1?style=for-the-badge&logo=mysql" alt="MySQL">
  <img src="https://img.shields.io/badge/AdminLTE-3.15-0073e6?style=for-the-badge" alt="AdminLTE">
</p>

---

## 📋 Descripción

Chankas Car es un sistema web completo diseñado para gestionar eficientemente las operaciones de un taller de conversión vehicular a GNV. Controla desde la recepción de vehículos hasta el pago de comisiones a técnicos, pasando por inventario, proveedores y generación de reportes.

## ✨ Características Principales

### 🔐 Autenticación y Autorización
- **Login moderno y responsivo** con diseño personalizado
- **Sistema de roles**: Admin, Cajero, Técnico, Solo Lectura
- **Middleware de autorización** por rol y permisos granulares
- **Gestión de usuarios** con estados activos/inactivos

### 👥 Gestión de Personal
- Registro de empleados con cargos
- Asignación de usuarios a empleados
- Control de técnicos y personal administrativo
- Histórico de trabajos por técnico

### 🔧 Gestión de Trabajos
- Registro de trabajos con servicios múltiples
- Búsqueda automática de clientes por placa
- Cálculo automático de comisiones para técnicos
- Fechas de trabajo, recepción y recalificación
- Vista especial para técnicos: "Mis Trabajos"
- Indicador de clientes recurrentes

### 💰 Gestión de Pagos
- Cálculo de saldos por técnico
- Registro de pagos parciales y totales
- **Historial de pagos** colapsable con DataTables
- Estadísticas del mes actual
- Exportación de reportes a PDF

### 📦 Inventario
- Control de stock en tiempo real
- Alertas de stock bajo (visual)
- Tipos de stock: Contable y Pregunta
- Relación con proveedores
- Uso de piezas por trabajo

### 🏢 Proveedores
- Gestión de proveedores
- Items asociados por proveedor
- Información de contacto completa

### 📊 Reportes y PDFs
- **Detalle de venta/trabajo** en PDF con marca personalizada
- Diseño profesional con colores corporativos
- Información completa: servicios, piezas, totales

### 📈 Sistema de Auditoría Avanzado
- **Dashboard de estadísticas** con gráficos
- Registro de todas las acciones del sistema
- **Filtros avanzados**: usuario, acción, módulo, fechas, búsqueda de texto
- **Top usuarios más activos**
- Vista detallada de cada log con cambios registrados
- Gráfico de dona con distribución de acciones
- Preparado para exportación a Excel

### 🎨 Interfaz de Usuario
- **Tema personalizado** con paleta de colores corporativa
- **SweetAlert2** para confirmaciones y notificaciones
- **DataTables** con traducción al español
- **Diseño responsivo** para móviles y tablets
- **Favicon personalizado** en todas las páginas
- Alertas flash automáticas (success/error/info)

### 🔄 Sistema de Backups
- Configurado con **Spatie Laravel Backup**
- Backups de base de datos con compresión Gzip
- Backups completos de archivos
- Comandos artisan disponibles
- Guía completa en `BACKUP_GUIDE.md`

## 🎨 Paleta de Colores

```css
--primary-dark: #1a3a47;   /* Azul oscuro principal */
--primary-blue: #1e5a7a;   /* Azul medio */
--primary-light: #6db3c8;  /* Azul claro/cyan */
--accent-yellow: #fbc02d;  /* Amarillo acento */
--dark-gray: #3d3d3d;      /* Gris oscuro */
```

## 🛠️ Tecnologías Utilizadas

### Backend
- **Laravel 10.49.1** - Framework PHP
- **PHP 8.1.33** - Lenguaje de programación
- **MySQL 8.0.44** - Base de datos
- **Laravel Breeze** - Autenticación
- **Spatie Laravel Backup 8.8** - Sistema de backups

### Frontend
- **AdminLTE 3.x** - Template de administración
- **Tailwind CSS** - Framework CSS utility-first
- **Vite** - Build tool y asset bundling
- **jQuery** - Manipulación DOM
- **DataTables 1.11.5** - Tablas interactivas
- **SweetAlert2 v11** - Alertas modernas
- **Chart.js v3** - Gráficos y estadísticas
- **Font Awesome 6** - Iconografía

### PDFs
- **DomPDF (barryvdh/laravel-dompdf v3.1)** - Generación de PDFs

## 📁 Estructura del Proyecto

```
chankascar/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Controladores (14 módulos)
│   │   └── Middleware/       # CheckRole middleware
│   ├── Models/               # Modelos Eloquent (14 modelos)
│   └── Traits/               # LogsActivity trait
├── database/
│   ├── migrations/           # 23 migraciones
│   └── seeders/
├── resources/
│   ├── css/
│   │   ├── adminlte-theme.css   # Tema personalizado
│   │   ├── app.css
│   │   └── palette.css
│   ├── js/
│   └── views/
│       ├── layouts/
│       │   └── base.blade.php   # Layout base con favicon y SweetAlert
│       ├── auth/
│       │   └── login.blade.php  # Login personalizado
│       ├── trabajos/
│       ├── clientes/
│       ├── empleados/
│       ├── pagos/
│       ├── activity-logs/       # Sistema de auditoría
│       └── ...
├── public/
│   ├── favicon.png              # Favicon del sistema
│   └── js/
│       └── sweetalert-helpers.js  # Helpers de SweetAlert
├── config/
│   ├── adminlte.php
│   └── backup.php               # Configuración de backups
├── routes/
│   └── web.php                  # Rutas con middleware de roles
├── BACKUP_GUIDE.md              # Guía de backups
└── README.md
```

## 🚀 Instalación

### Requisitos Previos
- PHP >= 8.1.33
- Composer >= 2.0
- Node.js >= 16.x y NPM
- MySQL >= 8.0.44
- Servidor web (Apache/Nginx)
- Sistema operativo: Linux (Probado en Xubuntu 22.04 LTS)
- Extensiones PHP: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, GD

### Pasos de Instalación

1. **Clonar el repositorio**
```bash
git clone https://github.com/tu-usuario/chankascar.git
cd chankascar
```

2. **Instalar dependencias de PHP**
```bash
composer install
```

3. **Instalar dependencias de Node.js**
```bash
npm install
```

4. **Configurar el archivo .env**
```bash
cp .env.example .env
php artisan key:generate
```

Editar `.env` con tus credenciales de base de datos:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chankascar_db
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

5. **Crear la base de datos**
```bash
mysql -u root -p
CREATE DATABASE chankascar_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

6. **Ejecutar migraciones**
```bash
php artisan migrate
```

7. **Ejecutar seeders (opcional)**
```bash
php artisan db:seed
```

8. **Compilar assets**
```bash
npm run build
# o para desarrollo:
npm run dev
```

9. **Configurar permisos**
```bash
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache
```

10. **Iniciar el servidor**
```bash
php artisan serve
```

Acceder a: `http://localhost:8000`

## 👤 Usuarios por Defecto

Si ejecutaste los seeders, puedes usar:

- **Admin**: admin@chankascar.com / password
- **Cajero**: cajero@chankascar.com / password
- **Técnico**: tecnico@chankascar.com / password

## 📝 Uso

### Roles y Permisos

#### Administrador
- Acceso total al sistema
- Gestión de usuarios y empleados
- Configuración de servicios, cargos, inventario
- Acceso a logs de actividad
- Gestión de proveedores y gastos

#### Cajero
- Gestión de clientes y trabajos
- Registro y cálculo de pagos a técnicos
- Generación de PDFs
- Consulta de inventario

#### Técnico
- Vista "Mis Trabajos" con trabajos asignados
- Consulta de comisiones ganadas
- Descarga de PDFs de trabajos

### Comandos Artisan Útiles

```bash
# Crear backup de base de datos
php artisan backup:run --only-db

# Crear backup completo
php artisan backup:run

# Ver lista de backups
php artisan backup:list

# Limpiar backups antiguos
php artisan backup:clean

# Ver estado de backups
php artisan backup:monitor
```

## 🔧 Desarrollo

### Compilar assets en modo desarrollo
```bash
npm run dev
```

### Compilar assets para producción
```bash
npm run build
```

### Ejecutar tests
```bash
php artisan test
```

## 📦 Backups

El sistema incluye un sistema completo de backups. Ver guía detallada en [BACKUP_GUIDE.md](BACKUP_GUIDE.md)

**Comandos principales:**
- `php artisan backup:run --only-db` - Backup solo de base de datos
- `php artisan backup:run` - Backup completo
- Ubicación: `storage/app/Chankas Car/`

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/NuevaCaracteristica`)
3. Commit tus cambios (`git commit -m 'Agregar nueva característica'`)
4. Push a la rama (`git push origin feature/NuevaCaracteristica`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo [LICENSE](LICENSE) para más detalles.

## 👨‍💻 Autor

**Pedro Antonio López Chumacero**  
*Chanka's Development Team*

## 🙏 Agradecimientos

- Laravel Framework
- AdminLTE Template
- Comunidad de desarrolladores de Laravel
- Todos los contribuidores del proyecto

---

<p align="center">
  Desarrollado con 💪 por <strong>Pedro Antonio López Chumacero</strong><br>
  Chanka's Development Team - Sucre, Bolivia
</p>