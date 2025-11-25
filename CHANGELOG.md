# Changelog

> **English version available**: [CHANGELOG.en.md](CHANGELOG.en.md)

Todos los cambios notables en este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto adhiere a [Semantic Versioning](https://semver.org/lang/es/).

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
