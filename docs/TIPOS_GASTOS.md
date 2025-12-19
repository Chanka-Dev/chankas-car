# Sistema de Gestión de Tipos de Gastos

## Descripción
Sistema CRUD completo para administrar los tipos/conceptos de gastos del taller. Permite crear, editar y eliminar tipos de gastos de manera centralizada.

## Funcionalidades

### 1. Listar Tipos de Gastos
- **Ruta**: `/tipos-gastos`
- **Vista**: `resources/views/tipos-gastos/index.blade.php`
- Muestra todos los tipos de gastos con filtros por nombre y estado
- Indica cuántos gastos están asociados a cada tipo
- Permite editar, ver detalles o eliminar (si no tiene registros)

### 2. Crear Tipo de Gasto
- **Ruta**: `/tipos-gastos/create`
- **Vista**: `resources/views/tipos-gastos/create.blade.php`
- Campos:
  - Nombre (obligatorio, único, 150 caracteres máx)
  - Descripción (opcional, 500 caracteres máx)
  - Estado (Activo/Inactivo)

### 3. Editar Tipo de Gasto
- **Ruta**: `/tipos-gastos/{id}/edit`
- **Vista**: `resources/views/tipos-gastos/edit.blade.php`
- Permite modificar nombre, descripción y estado
- Muestra advertencia si tiene gastos asociados
- Muestra fechas de creación y última actualización

### 4. Ver Detalles
- **Ruta**: `/tipos-gastos/{id}`
- **Vista**: `resources/views/tipos-gastos/show.blade.php`
- Muestra información completa del tipo de gasto
- Lista los últimos 10 gastos que usan este tipo
- Calcula el total de gastos asociados

### 5. Eliminar Tipo de Gasto
- Solo permite eliminar si **NO tiene gastos registrados**
- Si tiene gastos, debe desactivarlo en lugar de eliminarlo
- Confirmación con SweetAlert antes de eliminar

## Integración con Gastos

Los formularios de gastos (crear/editar) ahora obtienen los tipos desde la tabla `tipos_gastos` en lugar de conceptos únicos de gastos existentes.

**Cambios en GastoTallerController**:
```php
// Antes:
$conceptos = GastoTaller::select('concepto')
    ->distinct()
    ->orderBy('concepto')
    ->pluck('concepto');

// Ahora:
$conceptos = \App\Models\TipoGasto::activos()
    ->orderBy('nombre')
    ->pluck('nombre');
```

## Base de Datos

### Tabla: `tipos_gastos`
```sql
id_tipo_gasto: BIGINT PRIMARY KEY
nombre: VARCHAR(150) UNIQUE
descripcion: TEXT NULLABLE
activo: BOOLEAN DEFAULT TRUE
created_at: TIMESTAMP
updated_at: TIMESTAMP
```

### Migración
- Archivo: `database/migrations/2025_12_18_103440_create_tipos_gastos_table.php`

### Seeder
- Archivo: `database/seeders/TiposGastosSeeder.php`
- Pobla la tabla con:
  - Conceptos existentes de gastos_taller (14 encontrados)
  - Tipos comunes predefinidos (12 adicionales)
  - Total: 24 tipos de gastos iniciales

## Modelo

**Archivo**: `app/Models/TipoGasto.php`

**Métodos**:
- `scopeActivos($query)`: Filtra solo tipos activos
- `tieneGastos()`: Verifica si el tipo está siendo usado

**Relaciones**:
- No tiene relación directa con GastoTaller (se usa el nombre como referencia)

## Controlador

**Archivo**: `app/Http/Controllers/TipoGastoController.php`

**Middleware**: `role:admin` (solo administradores)

**Validaciones**:
- Nombre: requerido, único, máx 150 caracteres
- Descripción: opcional, máx 500 caracteres
- Estado: requerido, booleano

## Rutas

```php
Route::resource('tipos-gastos', TipoGastoController::class)->parameters([
    'tipos-gastos' => 'tiposGasto'
]);
```

**Rutas generadas**:
- GET `/tipos-gastos` - Listar
- GET `/tipos-gastos/create` - Formulario crear
- POST `/tipos-gastos` - Guardar
- GET `/tipos-gastos/{id}` - Ver detalles
- GET `/tipos-gastos/{id}/edit` - Formulario editar
- PUT `/tipos-gastos/{id}` - Actualizar
- DELETE `/tipos-gastos/{id}` - Eliminar

## Menú AdminLTE

Agregado en `config/adminlte.php` en la sección ADMINISTRACIÓN:

```php
[
    'text'   => 'Tipos de Gastos',
    'url'    => 'tipos-gastos',
    'icon'   => 'fas fa-fw fa-tags',
    'active' => ['tipos-gastos*'],
    'can'    => 'admin',
],
```

## Permisos

- **Ver/Gestionar**: Solo administradores (`role:admin`)
- **Usar en gastos**: Todos los usuarios con acceso a gastos (admin, cajero)

## Características Especiales

1. **Protección de datos**: No se pueden eliminar tipos con gastos registrados
2. **Estado activo/inactivo**: Los tipos inactivos no aparecen en nuevos registros
3. **Búsqueda y filtros**: En el listado principal
4. **Integración automática**: Los tipos activos aparecen automáticamente en el select de gastos
5. **Logs de actividad**: Todas las acciones se registran (trait LogsActivity)
6. **Validación de unicidad**: No se pueden crear tipos duplicados
7. **Historial de uso**: Vista de detalle muestra últimos gastos del tipo

## Ejecución

### Migrar la tabla:
```bash
php artisan migrate
```

### Poblar con datos iniciales:
```bash
php artisan db:seed --class=TiposGastosSeeder
```

### Limpiar cache:
```bash
php artisan config:clear
php artisan cache:clear
```

## Mejoras Futuras Sugeridas

1. Agregar categorías de gastos (fijos, variables, extraordinarios)
2. Establecer límites de gasto por tipo
3. Reportes por tipo de gasto
4. Gráficos de distribución de gastos por tipo
5. Notificaciones cuando un tipo excede cierto monto
6. Relación directa FK en lugar de usar el nombre como referencia

## Comandos Útiles

```bash
# Ver rutas de tipos-gastos
php artisan route:list --name=tipos-gastos

# Contar tipos activos
php artisan tinker --execute="echo App\Models\TipoGasto::activos()->count();"

# Ver tipos más usados
php artisan tinker --execute="DB::table('gastos_taller')->select('concepto', DB::raw('count(*) as total'))->groupBy('concepto')->orderByDesc('total')->limit(5)->get();"
```
