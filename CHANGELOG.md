# Changelog

> **English version available**: [CHANGELOG.en.md](CHANGELOG.en.md)

Todos los cambios notables en este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto adhiere a [Semantic Versioning](https://semver.org/lang/es/).

## [No publicado] - En desarrollo

### Agregado
- � **Dashboard rediseñado con filtros de fecha**
  - Filtro de rango de fechas (fecha desde/hasta) con valores por defecto (mes actual)
  - Métricas principales: Trabajos, Ingresos, Comisiones y Gastos del periodo
  - Resumen financiero: Utilidad Neta prominente como único KPI consolidado
  - Ingresos por servicio y servicios más solicitados filtrados por fecha
  - Últimos 10 trabajos sin filtro (vista general)
  - Caché dinámico basado en rango de fechas seleccionado
  - Eliminación de métricas estáticas redundantes (empleados, clientes)
  - Botón "Mes actual" para resetear rápidamente al periodo corriente

- 🏷️ **Sistema de Tipos de Gastos**
  - Gestión completa CRUD de tipos de gastos predefinidos
  - 24 tipos iniciales cargados vía seeder (AGUA, LUZ, INTERNET, etc.)
  - Prevención de eliminación de tipos con registros asociados
  - Estados activo/inactivo para control de visibilidad
  - Modal de creación rápida (+) en formularios de gastos
  - Integración con Select2 para búsqueda inteligente
  - Vista detallada con últimos 10 gastos por tipo
  - Contador de registros asociados por tipo
  - Acceso dual: módulo admin + botón quick-add
  - Auto-capitalización de nombres de tipos
  - Logging de actividades con Spatie\Activitylog
### Cambiado
- 📊 **DashboardController refactorizado** (19 Dic 2025)
  - Método `index()` ahora acepta parámetros de fecha via Request
  - Cambio de consultas mensuales (`whereMonth/whereYear`) a rangos (`whereBetween`)
  - Variables renombradas: `*MesActual` → `*Periodo` para mayor claridad
  - Cache keys dinámicos: `md5($fechaDesde . $fechaHasta)` para evitar conflictos
  - Eliminada consulta `gastosPorConcepto` (tabla removida del dashboard)
  - Vista retorna `fechaDesde` y `fechaHasta` para mantener filtros en forms

- 💸 **GastoTallerController actualizado** (18 Dic 2025)
  - Métodos `create()` y `edit()` obtienen conceptos desde `TipoGasto::activos()`
  - Cambio de conceptos únicos de BD a tipos predefinidos centralizados
  - Mejora en consistencia: todos los gastos usan tipos estandarizados

- 🎨 **Vista de dashboard simplificada** (19 Dic 2025)
  - Removidas 2 small-boxes redundantes (Empleados Activos, Clientes Registrados)
  - Removida sección "Ingresos" duplicada (info-box redundante)
  - Removida tabla "Gastos por Concepto" completa
  - Tabla "Últimos Trabajos" expandida a ancho completo (col-md-12)
  - Utilidad Neta ahora es único info-box financiero destacado
  - Textos actualizados: "Este Mes" → "del Periodo" en todas las métricas
  - Mensajes de "no hay datos" ahora mencionan "periodo seleccionado"

- 🎨 **Formularios de gastos mejorados** (18-19 Dic 2025)
  - Campo `concepto` de datalist a Select2 con temas Bootstrap 4
  - Input-group con botón [+] verde para quick-add (solo admins)
  - Modal integrado para crear tipos sin abandonar formulario
  - Select2 con placeholder mejorado y estilos nativos de dropdown
  - Estilos CSS personalizados (50+ líneas) para hover/focus states
- �🛡️ **Seguridad Express - Fase 1** (Protección dominio público)
  - **Fail2Ban integrado**: Bloqueo automático de IPs tras 5 intentos fallidos de login
  - **Google reCAPTCHA v3**: Protección invisible contra bots en login y registro
  - **Security Headers HTTP**: HSTS, CSP, X-Frame-Options, X-Content-Type-Options
  - Logging de intentos de login fallidos con IP, email y user agent
  - Configuración de cookies seguras (httpOnly, sameSite, secure)
  - Jail personalizada para Laravel con bantime de 1 hora
  - Middleware `VerifyRecaptcha` con score mínimo de 0.5
  - Middleware `SecurityHeaders` aplicado globalmente

- 💼 **Vista agrupada de pagos a técnicos**
  - Nueva vista `/pagos/agrupado` con servicios consolidados por fecha y tipo
  - Agrupa cantidades de servicios idénticos (ej: "3x LAVADO SIMPLE")
  - Exportación PDF con formato optimizado para impresión
  - Botón de alternancia entre vista detallada y agrupada
  - Facilita lectura rápida de comisiones por tipo de servicio

- 🔧 **Comando de verificación de comisiones MANT**
  - `php artisan check:mant` - Compara comisiones Excel vs BD
  - Detecta servicios de mantenimiento con discrepancias
  - Reporta diferencias en precios técnicos
  - Soporte para archivos Excel personalizados con `--file=`
  - Identifica servicios faltantes en base de datos

- 🎯 **Select2 mejorado en gastos**
  - Campo `concepto` convertido de datalist a Select2
  - Búsqueda inteligente en conceptos existentes
  - Soporte para crear nuevos conceptos con etiqueta "(nuevo)"
  - Estilos mejorados: bordes, flechas visibles, efectos hover/focus
  - Layout responsive: ancho 50% (col-md-6) para mejor legibilidad
  - Carga dinámica desde BD de conceptos únicos ordenados

- 🎨 **Select2 mejorado en trabajos**
  - Selector de piezas convertido a Select2 con búsqueda
  - Estilos CSS unificados (50+ líneas) para apariencia de dropdown nativo
  - Inicialización automática en piezas agregadas manualmente
  - Inicialización automática en piezas cargadas desde servicios
  - Temas Bootstrap 4 con placeholder en cursiva
  - Efectos visuales: border-color #80bdff en hover/focus con box-shadow

### Cambiado
- 🔐 **Permisos de gastos reasignados**
  - Movido de middleware `admin` a `admin,cajero`
  - Cajeros ahora pueden gestionar gastos del taller
  - Alineado con permisos de trabajos y pagos
  - Eliminado `except(['index', 'show'])` para mayor seguridad

- 📊 **Controller de pagos extendido**
  - Agregados métodos `indexAgrupado()` y `exportarPdfAgrupado()`
  - Lógica de agrupación por fecha → servicio → cantidades
  - Cálculo de subtotales por día y tipo de servicio
  - Mismo sistema de filtros que vista detallada

### Arreglado
- 🔍 **Query de conceptos en GastoTallerController**
  - Agregada consulta `$conceptos` en métodos `create()` y `edit()`
  - Obtiene lista única de conceptos ordenados alfabéticamente
  - Alimenta Select2 con datos reales de la base de datos

- 🌐 **Rutas de pagos agrupados**
  - `GET /pagos/agrupado` → `pagos.index-agrupado`
  - `GET /pagos/exportar-pdf-agrupado` → `pagos.exportar-pdf-agrupado`
  - Integradas en middleware `admin,cajero`

### Técnico
- **3 archivos nuevos**:
  - `app/Console/Commands/CheckMantComisiones.php` - Comando de verificación
  - `resources/views/pagos/index-agrupado.blade.php` - Vista agrupada completa
  - `resources/views/pagos/pdf-agrupado.blade.php` - PDF compacto optimizado
- **10 archivos modificados**:
  - Controllers: `GastoTallerController.php`, `PagoController.php`
  - Vistas gastos: `create.blade.php`, `edit.blade.php` (Select2 + CSS)
  - Vistas trabajos: `create.blade.php`, `edit.blade.php` (Select2 piezas + CSS)
  - Vistas pagos: `index.blade.php` (botón vista agrupada)
  - Rutas: `web.php` (rutas agrupadas + permisos gastos)
- **Dependencias**: Select2 4.1.0-rc.0, Select2-Bootstrap4-Theme 1.5.2
- **Compatibilidad**: col-md-6 (50% ancho) para fields individuales, col-md-4 para pares

---

## [1.0.1] - 2025-11-26

### Agregado
- 🔍 **Select2 activado globalmente**
  - Búsqueda inteligente en formularios de trabajo
  - Interfaz mejorada para seleccionar servicios
  - Traducción al español de mensajes
  - Auto-inicialización en servicios dinámicos

- ⚡ **Autocompletado inteligente de teléfono**
  - Búsqueda AJAX en tiempo real al escribir placa (500ms debounce)
  - Funciona tanto en crear como editar trabajos
  - Indicadores visuales: cliente nuevo vs existente
  - Reduce errores de entrada de datos

- 🛡️ **Protección contra ataques de fuerza bruta (Rate Limiting)**
  - **Login**: Máximo 5 intentos por minuto
  - **Registro**: Máximo 3 registros cada 10 minutos
  - **Recuperación de contraseña**: Máximo 3 intentos cada 10 minutos
  - **Rutas autenticadas**: 120 peticiones por minuto
  - **Búsquedas AJAX**: 30 peticiones por minuto
  - Página de error 429 personalizada con temporizador
  - Bloqueo temporal automático de IP sospechosas

- 🔒 **Validación mejorada de inputs con Regex**
  - **Prevención de SQL Injection**: Validación estricta de caracteres
  - **Prevención de XSS**: Bloqueo de scripts maliciosos
  - **Clientes**: Placas (A-Z0-9-), Teléfono (+0-9()-espacios)
  - **Empleados**: CI (solo números), Nombres/Apellidos (letras con ñ/tildes)
  - **Servicios**: Nombres validados, límites numéricos (max 999,999.99 Bs)
  - **Inventario**: Nombres seguros, stocks limitados (max 999,999)
  - **Trabajos**: Observaciones sanitizadas, fechas lógicas, límites de servicios/piezas
  - Mensajes de error personalizados y descriptivos
  - Conversión automática de placas a mayúsculas

### Mejorado
- 🛡️ **Validación de eliminación con integridad referencial**
  - **Clientes**: No permite eliminar si tiene trabajos asociados
  - **Empleados**: Verifica trabajos y pagos antes de eliminar
  - **Servicios**: Protege servicios con historial de uso
  - **Inventario**: Bloquea items usados en trabajos
  - Mensajes descriptivos con contadores específicos
  - Botón "Protegido" visual cuando no se puede eliminar

- 🎨 **Mejoras visuales**
  - Preloader mejorado sin borde circular
  - Animación de logo cambiada de wobble a pulse
  - Select2 con altura consistente en Bootstrap 4

- 📊 **Optimizaciones de rendimiento**
  - Servicios ordenados alfabéticamente en selects
  - Contador de trabajos precargado en vista de servicios
  - Relación `trabajoServicios` añadida al modelo Servicio

### Corregido
- 🐛 Mensajes de error genéricos reemplazados por específicos
- 🐛 Validación mejorada de eliminación en cascada
- 🐛 Select2 no se inicializaba en servicios agregados dinámicamente

---

## [1.0.0] - 2025-11-25

### Agregado
- ✨ **Sistema de autenticación personalizado**
  - Login moderno con diseño responsivo y animaciones
  - Gradientes y efectos glassmorphism
  - Paleta de colores corporativa Chankas Car
  - Loading spinner al iniciar sesión

- 🔐 **Sistema de roles y permisos**
  - Middleware CheckRole implementado
  - 4 roles: Admin, Cajero, Técnico, Solo Lectura
  - Rutas protegidas por rol
  - Permisos granulares en vistas

- 👥 **Gestión de empleados y cargos**
  - CRUD completo de empleados
  - Gestión de cargos
  - Relación usuario-empleado
  - Validación de CI único

- 🔧 **Gestión de trabajos**
  - Registro de trabajos con múltiples servicios
  - Búsqueda automática de clientes por placa
  - Cálculo automático de comisiones
  - Fechas de trabajo, recepción y recalificación
  - Uso de piezas de inventario en trabajos
  - Observaciones por trabajo
  - Indicador de clientes recurrentes (visitas)

- 👨‍🔧 **Vista especial para técnicos**
  - Ruta "/mis-trabajos" exclusiva para técnicos
  - Vista de trabajos asignados al técnico autenticado
  - Resumen de ganancias por trabajo
  - Totales calculados automáticamente

- 💰 **Sistema de pagos a técnicos**
  - Cálculo de saldos pendientes
  - Registro de pagos parciales y totales
  - Historial de pagos colapsable con DataTables
  - Estadísticas del mes actual
  - Total de pagos realizados
  - Exportación a PDF

- 🧾 **Generación de PDFs**
  - Detalle de venta/trabajo profesional
  - Diseño con colores corporativos
  - Información completa: cliente, servicios, piezas, totales
  - Sección de firmas
  - Descarga directa desde la vista

- 📦 **Gestión de inventario**
  - CRUD completo de items
  - Control de stock en tiempo real
  - Alertas visuales de stock bajo
  - Tipos de stock: Contable y Pregunta
  - Unidades de medida configurables
  - Relación con proveedores
  - Precios de compra y venta

- 🏢 **Gestión de proveedores**
  - CRUD completo
  - Información de contacto
  - Contador de items asociados
  - Soporte para parámetros personalizados en rutas

- 💼 **Gestión de clientes**
  - Búsqueda por placa
  - Historial de trabajos por cliente
  - Teléfono de contacto
  - Actualización automática al crear trabajo

- 📊 **Sistema de auditoría avanzado**
  - Dashboard con 4 estadísticas principales
  - Gráfico de dona con Chart.js
  - Top 5 usuarios más activos
  - Filtros avanzados colapsables
  - Búsqueda por texto en descripciones
  - Vista de tabla mejorada con badges de colores
  - Vista de detalles completa con historial
  - Tabla de cambios antes/después
  - Registro de IP y user agent
  - Preparado para exportación a Excel

- 🎨 **Tema personalizado**
  - Archivo adminlte-theme.css con paleta corporativa
  - Colores: #1a3a47, #6db3c8, #fbc02d
  - Sidebar personalizado
  - Cards con bordes de colores
  - Botones estilizados
  - Tablas con hover effects

- 🔔 **Sistema de notificaciones**
  - SweetAlert2 v11 integrado
  - Helpers reutilizables en sweetalert-helpers.js
  - Confirmaciones de eliminación personalizadas
  - Alertas automáticas para mensajes flash
  - Funciones: confirmarEliminacion(), mostrarExito(), mostrarError(), mostrarInfo()

- 📱 **Diseño responsivo**
  - Layout base con favicon en todas las páginas
  - Adaptable a móviles y tablets
  - DataTables responsivas
  - Menú colapsable

- 💾 **Sistema de backups**
  - Spatie Laravel Backup v8.8 configurado
  - Backups de base de datos con compresión Gzip
  - Backups completos de archivos
  - Exclusiones optimizadas
  - Comandos artisan disponibles
  - Guía completa en BACKUP_GUIDE.md

- 📚 **Documentación**
  - README.md completo y profesional
  - BACKUP_GUIDE.md con instrucciones detalladas
  - CHANGELOG.md para historial de cambios
  - Comentarios en código

### Cambiado
- 🔄 **Vistas principales migradas a layouts.base**
  - Clientes, Trabajos, Empleados, Servicios
  - Inventarios, Proveedores
  - Activity Logs
  - Eliminadas alertas Bootstrap manuales
  - Uso de @push('scripts') en lugar de @section('js')

- 🔧 **Rutas reorganizadas**
  - Agrupadas por nivel de permiso
  - Admin: acceso total
  - Admin + Cajero: gestión operativa
  - Técnico: solo sus trabajos

- 🎯 **Botones de eliminación mejorados**
  - Reemplazo de confirm() nativo por SweetAlert
  - Mensajes personalizados por módulo
  - IDs únicos en formularios

### Corregido
- 🐛 **Bug de pluralización en Proveedor**
  - Agregado getRouteKeyName() retornando 'id_proveedor'
  - Parámetros personalizados en route resource
  - Laravel ya no genera "proveedore"

- 🐛 **JavaScript incompleto en trabajo/create**
  - Agregado event handler para btn-agregar-servicio
  - Función calcularTotales() implementada
  - Select de servicios funcional

- 🐛 **Imports faltantes en TrabajoController**
  - TrabajoInventario importado
  - Inventario importado
  - Barryvdh\DomPDF\Facade\Pdf importado

- 🐛 **Favicon no visible**
  - Renombrado favicon.ico a favicon.png (formato correcto)
  - Layout base con links múltiples
  - Cache busting con versión

### Seguridad
- 🔒 Middleware de roles implementado en todas las rutas sensibles
- 🔒 Verificación de usuario activo en CheckRole
- 🔒 CSRF tokens en todos los formularios
- 🔒 Validación de inputs en controladores
- 🔒 Logs de actividad para auditoría

## Próximas Versiones

### [1.1.0] - Planificado
- [ ] Validación de stock en tiempo real
- [ ] Dashboard con estadísticas y gráficos
- [ ] Búsqueda global rápida
- [ ] Recordatorios de recalificación
- [ ] Exportación de logs a Excel
- [ ] Historial completo del vehículo
- [ ] Sistema de cotizaciones

### [1.2.0] - Planificado
- [ ] Reportes financieros avanzados
- [ ] Integración con WhatsApp Business
- [ ] Multi-moneda (Bs/USD)
- [ ] Caché de consultas frecuentes
- [ ] Tests unitarios completos

---

**Desarrollado por**: Pedro Antonio López Chumacero - Chanka's Development Team - Sucre, Bolivia  
**Nota**: Versiones anteriores a 1.0.0 no están documentadas ya que este es el primer release oficial.
