# Guía de Backups - Chankas Car

## Sistema de Backups Automáticos

Este proyecto utiliza `spatie/laravel-backup` para gestionar backups de la base de datos y archivos.

## Comandos Disponibles

### Crear un backup completo (Base de datos + Archivos)
```bash
php artisan backup:run
```

### Crear backup solo de la base de datos (Recomendado para backups frecuentes)
```bash
php artisan backup:run --only-db
```

### Crear backup solo de archivos
```bash
php artisan backup:run --only-files
```

### Ver lista de backups existentes
```bash
php artisan backup:list
```

### Limpiar backups antiguos (mantiene solo los más recientes según configuración)
```bash
php artisan backup:clean
```

### Verificar salud de los backups
```bash
php artisan backup:monitor
```

## Ubicación de los Backups

Los backups se guardan en: `/var/www/chankascar/storage/app/Chankas Car/`

Formato del nombre: `chankascar-YYYY-MM-DD-HH-II-SS.zip`

## Configuración de Backups Automáticos (Cron)

Para ejecutar backups automáticos, agregar al crontab:

```bash
# Editar crontab
crontab -e

# Agregar las siguientes líneas:

# Backup completo diario a las 2 AM
0 2 * * * cd /var/www/chankascar && php artisan backup:run --only-db >> /dev/null 2>&1

# Backup semanal completo (base de datos + archivos) los domingos a las 3 AM
0 3 * * 0 cd /var/www/chankascar && php artisan backup:run >> /dev/null 2>&1

# Limpiar backups antiguos diariamente a las 4 AM
0 4 * * * cd /var/www/chankascar && php artisan backup:clean >> /dev/null 2>&1
```

## Restauración de Backups

### 1. Restaurar Base de Datos

```bash
# Extraer el backup
cd /var/www/chankascar/storage/app/Chankas\ Car/
unzip chankascar-YYYY-MM-DD-HH-II-SS.zip -d restore_temp

# Encontrar el archivo .sql.gz
cd restore_temp/db-dumps

# Descomprimir
gunzip chankascar_db-YYYY-MM-DD-HH-II-SS.sql.gz

# Restaurar a MySQL
mysql -u [usuario] -p chankascar_db < chankascar_db-YYYY-MM-DD-HH-II-SS.sql

# Limpiar archivos temporales
cd ../..
rm -rf restore_temp
```

### 2. Restaurar Archivos

```bash
# Extraer archivos específicos del backup
unzip chankascar-YYYY-MM-DD-HH-II-SS.zip "public/*" -d /tmp/restore
# Copiar archivos necesarios según sea requerido
```

## Política de Retención

Configuración actual (en `config/backup.php`):
- Se mantienen todos los backups de los últimos 7 días
- Se mantiene 1 backup por día de las últimas 4 semanas
- Se mantiene 1 backup por semana de los últimos 3 meses
- Se mantiene 1 backup por mes del último año
- Backups más antiguos se eliminan automáticamente

## Qué se incluye en los Backups

### Base de Datos
- ✅ Todas las tablas de la base de datos MySQL
- ✅ Compresión Gzip para ahorrar espacio

### Archivos (solo en backup completo)
- ✅ Código fuente de la aplicación
- ✅ Archivos públicos (imágenes, PDFs generados, etc.)
- ✅ Configuraciones

### Excluidos de los Backups
- ❌ `vendor/` (dependencias de Composer - se pueden reinstalar)
- ❌ `node_modules/` (dependencias de NPM - se pueden reinstalar)
- ❌ `storage/logs/` (logs - no críticos)
- ❌ `storage/framework/cache/` (caché - temporal)
- ❌ `storage/framework/sessions/` (sesiones - temporal)

## Recomendaciones

1. **Backups Externos**: Copiar periódicamente los backups a un servidor externo o servicio en la nube
   ```bash
   scp /var/www/chankascar/storage/app/Chankas\ Car/*.zip usuario@servidor-remoto:/backups/
   ```

2. **Verificar Backups**: Probar la restauración periódicamente para asegurar que funcionan

3. **Monitoreo**: Revisar regularmente `php artisan backup:monitor` para detectar problemas

4. **Espacio en Disco**: Monitorear el espacio disponible en `storage/app/`
   ```bash
   du -sh /var/www/chankascar/storage/app/Chankas\ Car/
   ```

## Solución de Problemas

### Error: Permission denied en storage/logs/
```bash
sudo chmod -R 775 storage
sudo chown -R $USER:www-data storage
```

### Backup muy pesado
- Usar `--only-db` para backups frecuentes
- Backups completos solo semanalmente

### MySQL dump timeout
Agregar en `config/database.php`:
```php
'mysql' => [
    // ...
    'dump' => [
        'timeout' => 60 * 5, // 5 minutos
    ],
],
```

## Contacto y Soporte

Para más información sobre backups, consultar:
- Documentación oficial: https://spatie.be/docs/laravel-backup/
- Código del proyecto: `/var/www/chankascar/config/backup.php`
