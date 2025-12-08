# Guía de Automatización WhatsApp con N8N - Chankas Car

**Fecha:** 6 de Diciembre 2025
**Versión N8N:** 1.122.5
**Estado:** ✅ Servicio activo y funcionando

## 📋 Estado Actual del Sistema

### Verificación del Servicio N8N
```bash
# Contenedor: n8n-docker_n8n_1
# Estado: Up 12 hours
# Puerto: 5678 (accesible en 0.0.0.0:5678)
# Versión: 1.122.5 (actualizado hace 2 días)
# Base de datos: SQLite (/home/node/.n8n/database.sqlite)
```

### URLs de Acceso
- **Panel N8N Local:** http://localhost:5678
- **Panel N8N Red Local:** http://10.49.1:5678
- **API N8N:** http://localhost:5678/api/v1

---

## 🎯 Objetivo de la Automatización

Automatizar el envío de mensajes de WhatsApp a clientes cuando se realicen trabajos específicos, consultando directamente la base de datos MySQL de Laravel.

### Casos de Uso
1. **Notificación post-servicio:** Mensaje de agradecimiento después de realizar un trabajo
2. **Recordatorio de mantenimiento:** Alertas programadas según servicios realizados
3. **Promociones personalizadas:** Ofertas basadas en el historial del cliente
4. **Seguimiento de calidad:** Encuestas de satisfacción

---

## 🗄️ Información de Base de Datos

### Credenciales MySQL
```env
DB_HOST: 127.0.0.1
DB_PORT: 3306
DB_DATABASE: chankascar_db
DB_USERNAME: chankascar_user
DB_PASSWORD: ChAnKaS2024$eGuRo
```

### Tablas Relevantes para Automatización

#### 1. `trabajos` - Trabajos realizados
```sql
Campos principales:
- id_trabajo (PK)
- id_cliente (FK)
- id_empleado (FK)
- fecha_trabajo (DATE)
- total_cliente (DECIMAL)
- total_tecnico (DECIMAL)
- observaciones (TEXT)
- created_at, updated_at
```

#### 2. `clientes` - Información de clientes
```sql
Campos principales:
- id_cliente (PK)
- nombre (VARCHAR)
- placas (VARCHAR) - Placa del vehículo
- telefono (VARCHAR) - ⚠️ IMPORTANTE para WhatsApp
- email (VARCHAR)
- created_at, updated_at
```

#### 3. `trabajo_servicios` - Servicios realizados en cada trabajo
```sql
Campos principales:
- id_trabajo_servicio (PK)
- id_trabajo (FK)
- id_servicio (FK)
- cantidad (INT)
- importe_cliente (DECIMAL)
- importe_tecnico (DECIMAL)
- observaciones (TEXT)
```

#### 4. `servicios` - Catálogo de servicios
```sql
Campos principales:
- id_servicio (PK)
- nombre (VARCHAR) - Ej: "HABILITACION SIMPLE", "MANTENIMIENTO COMPLETO"
- costo (DECIMAL)
- comision (DECIMAL)
```

---

## 🔧 Configuración Inicial en N8N

### Paso 1: Acceder al Panel
```bash
# Abrir en navegador
http://localhost:5678
```

### Paso 2: Crear Credenciales de MySQL
1. En N8N: **Settings** → **Credentials** → **Add Credential**
2. Buscar: **MySQL**
3. Configurar:
   ```
   Host: 127.0.0.1
   Port: 3306
   Database: chankascar_db
   User: chankascar_user
   Password: ChAnKaS2024$eGuRo
   Connection Timeout: 30000
   ```
4. **Test** → Guardar como "Chankas Car DB"

### Paso 3: Configurar WhatsApp Business API

#### Opción 1: WhatsApp Business API Oficial (Recomendada)
- Requiere cuenta de Meta Business
- Mayor confiabilidad
- Costos asociados
- Templates aprobados por Meta

#### Opción 2: Evolution API (Alternativa gratuita)
- API no oficial basada en Baileys
- Instalación local con Docker
- Sin costos
- Mayor flexibilidad en mensajes

**Para comenzar, recomiendo Evolution API:**

```bash
# Instalar Evolution API con Docker
docker run -d \
  --name evolution-api \
  -p 8080:8080 \
  -e EVOLUTION_API_KEY=TU_API_KEY_SEGURA \
  atendai/evolution-api:latest
```

---

## 📱 Flujos de Automatización Recomendados

### Flujo 1: Mensaje Post-Trabajo (Básico)

**Trigger:** Cron Schedule (cada 5 minutos)

**Pasos del Workflow:**

1. **MySQL Query** - Obtener trabajos recientes sin notificar
```sql
SELECT 
    t.id_trabajo,
    t.fecha_trabajo,
    c.nombre as cliente_nombre,
    c.telefono,
    c.placas,
    t.total_cliente,
    GROUP_CONCAT(s.nombre SEPARATOR ', ') as servicios
FROM trabajos t
INNER JOIN clientes c ON t.id_cliente = c.id_cliente
INNER JOIN trabajo_servicios ts ON t.id_trabajo = ts.id_trabajo
INNER JOIN servicios s ON ts.id_servicio = s.id_servicio
WHERE t.created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
    AND c.telefono IS NOT NULL 
    AND c.telefono != ''
    AND NOT EXISTS (
        SELECT 1 FROM notificaciones_enviadas ne 
        WHERE ne.id_trabajo = t.id_trabajo 
        AND ne.tipo = 'whatsapp_post_trabajo'
    )
GROUP BY t.id_trabajo
```

2. **IF Node** - Verificar si hay trabajos
   - Condición: `{{ $json.length > 0 }}`

3. **Loop Over Items** - Iterar cada trabajo

4. **Function Node** - Formatear mensaje
```javascript
const cliente = $input.item.json.cliente_nombre;
const placa = $input.item.json.placas || 'su vehículo';
const servicios = $input.item.json.servicios;
const total = parseFloat($input.item.json.total_cliente).toFixed(2);

const mensaje = `¡Hola ${cliente}! 👋

Gracias por confiar en Chankas Car 🚗

Hemos completado el servicio de ${servicios} para ${placa}.

Total: Bs ${total}

¿Todo está bien? Nos encantaría conocer tu opinión.

¡Esperamos verte pronto! 😊`;

return {
  telefono: $input.item.json.telefono,
  mensaje: mensaje,
  id_trabajo: $input.item.json.id_trabajo
};
```

5. **HTTP Request** - Enviar WhatsApp (Evolution API)
```
Method: POST
URL: http://localhost:8080/message/sendText/INSTANCE_NAME
Headers:
  apikey: TU_API_KEY
Body:
{
  "number": "591{{$json.telefono}}@s.whatsapp.net",
  "text": "{{$json.mensaje}}"
}
```

6. **MySQL Insert** - Registrar envío
```sql
INSERT INTO notificaciones_enviadas 
(id_trabajo, tipo, destinatario, mensaje, enviado_at) 
VALUES 
(?, 'whatsapp_post_trabajo', ?, ?, NOW())
```

---

### Flujo 2: Recordatorio de Mantenimiento Programado

**Trigger:** Cron Schedule (diario a las 9:00 AM)

**Query:** Clientes con último mantenimiento hace más de 3 meses
```sql
SELECT 
    c.nombre,
    c.telefono,
    c.placas,
    MAX(t.fecha_trabajo) as ultimo_mantenimiento,
    DATEDIFF(NOW(), MAX(t.fecha_trabajo)) as dias_desde_mantenimiento
FROM clientes c
INNER JOIN trabajos t ON c.id_cliente = t.id_cliente
INNER JOIN trabajo_servicios ts ON t.id_trabajo = ts.id_trabajo
INNER JOIN servicios s ON ts.id_servicio = s.id_servicio
WHERE s.nombre LIKE '%MANTENIMIENTO%'
    AND c.telefono IS NOT NULL
GROUP BY c.id_cliente
HAVING dias_desde_mantenimiento > 90
    AND NOT EXISTS (
        SELECT 1 FROM notificaciones_enviadas ne 
        WHERE ne.destinatario = c.telefono 
        AND ne.tipo = 'recordatorio_mantenimiento'
        AND ne.enviado_at > DATE_SUB(NOW(), INTERVAL 30 DAY)
    )
```

**Mensaje:**
```
¡Hola [NOMBRE]! 👋

Han pasado [X] días desde tu último mantenimiento en Chankas Car.

Es momento de revisar tu vehículo [PLACA] para mantenerlo en óptimas condiciones 🚗✨

¿Agendamos una cita?

Chankas Car - Tu taller de confianza
```

---

## 📊 Tabla de Control de Notificaciones

### Crear tabla en MySQL
```sql
CREATE TABLE notificaciones_enviadas (
    id_notificacion INT AUTO_INCREMENT PRIMARY KEY,
    id_trabajo INT NULL,
    id_cliente INT NULL,
    tipo ENUM('whatsapp_post_trabajo', 'recordatorio_mantenimiento', 'promocion', 'encuesta') NOT NULL,
    destinatario VARCHAR(20) NOT NULL,
    mensaje TEXT NOT NULL,
    estado ENUM('enviado', 'fallido', 'pendiente') DEFAULT 'enviado',
    error_mensaje TEXT NULL,
    enviado_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_trabajo (id_trabajo),
    INDEX idx_cliente (id_cliente),
    INDEX idx_tipo_fecha (tipo, enviado_at),
    INDEX idx_destinatario (destinatario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 🚀 Pasos para Implementar

### 1. Preparar Base de Datos
```bash
# Conectar a MySQL
mysql -u chankascar_user -p'ChAnKaS2024$eGuRo' chankascar_db

# Crear tabla de notificaciones
SOURCE /ruta/a/crear_tabla_notificaciones.sql
```

### 2. Instalar Evolution API (si se elige esta opción)
```bash
# Crear directorio de datos
mkdir -p ~/evolution-api-data

# Ejecutar contenedor
docker run -d \
  --name evolution-api \
  --network n8n-docker_default \
  -p 8080:8080 \
  -v ~/evolution-api-data:/evolution/instances \
  -e EVOLUTION_API_KEY=ChankasCarN8N2024! \
  atendai/evolution-api:latest

# Verificar
curl http://localhost:8080/
```

### 3. Configurar Instancia de WhatsApp
```bash
# Crear instancia
curl -X POST http://localhost:8080/instance/create \
  -H "apikey: ChankasCarN8N2024!" \
  -H "Content-Type: application/json" \
  -d '{
    "instanceName": "chankas-car",
    "token": "OPTIONAL_TOKEN",
    "qrcode": true
  }'

# Obtener QR Code para vincular WhatsApp
curl http://localhost:8080/instance/connect/chankas-car \
  -H "apikey: ChankasCarN8N2024!"
```

### 4. Crear Workflows en N8N
1. Acceder a http://localhost:5678
2. **New Workflow** → Nombrar "WhatsApp Post-Trabajo"
3. Agregar nodos según Flujo 1
4. Activar workflow

---

## 📈 Monitoreo y Logs

### Verificar envíos en base de datos
```sql
-- Últimos 10 mensajes enviados
SELECT * FROM notificaciones_enviadas 
ORDER BY enviado_at DESC 
LIMIT 10;

-- Estadísticas por tipo
SELECT 
    tipo,
    COUNT(*) as total_enviados,
    SUM(CASE WHEN estado = 'enviado' THEN 1 ELSE 0 END) as exitosos,
    SUM(CASE WHEN estado = 'fallido' THEN 1 ELSE 0 END) as fallidos
FROM notificaciones_enviadas
GROUP BY tipo;

-- Envíos de hoy
SELECT COUNT(*) as envios_hoy
FROM notificaciones_enviadas
WHERE DATE(enviado_at) = CURDATE();
```

### Logs de N8N
```bash
# Ver logs del contenedor
docker logs n8n-docker_n8n_1 --tail 100 -f

# Logs de eventos dentro del contenedor
docker exec n8n-docker_n8n_1 tail -f /home/node/.n8n/n8nEventLog.log
```

---

## ⚠️ Consideraciones Importantes

### Formato de Teléfonos
- Los números en la BD deben estar en formato: `73478728` (sin prefijo)
- N8N agregará el prefijo de Bolivia: `591` + número
- Formato WhatsApp: `59173478728@s.whatsapp.net`

### Límites y Buenas Prácticas
- ⏰ No enviar más de 1 mensaje por cliente por día
- 🕐 Respetar horarios (9:00 AM - 8:00 PM)
- ✅ Siempre registrar envíos en tabla de control
- 🔄 Implementar reintentos para mensajes fallidos
- 📊 Monitorear tasa de entrega

### Seguridad
- 🔐 API keys seguras (nunca en código)
- 🚫 No exponer N8N públicamente sin autenticación
- 📝 Logs de auditoría de todos los envíos
- 🔒 Credenciales de BD solo para N8N

---

## 🔍 Troubleshooting

### N8N no responde
```bash
docker restart n8n-docker_n8n_1
docker logs n8n-docker_n8n_1 --tail 50
```

### No se conecta a MySQL
```bash
# Verificar desde contenedor N8N
docker exec -it n8n-docker_n8n_1 sh
nc -zv 127.0.0.1 3306
```

### WhatsApp no envía mensajes
```bash
# Verificar estado de instancia
curl http://localhost:8080/instance/fetchInstances \
  -H "apikey: ChankasCarN8N2024!"

# Verificar conexión de WhatsApp
curl http://localhost:8080/instance/connectionState/chankas-car \
  -H "apikey: ChankasCarN8N2024!"
```

---

## 📚 Recursos Adicionales

- **Documentación N8N:** https://docs.n8n.io/
- **Evolution API:** https://github.com/EvolutionAPI/evolution-api
- **WhatsApp Business API:** https://developers.facebook.com/docs/whatsapp

---

## ✅ Checklist de Implementación

- [ ] N8N funcionando correctamente
- [ ] Credenciales MySQL configuradas en N8N
- [ ] Tabla `notificaciones_enviadas` creada
- [ ] Evolution API instalada y configurada
- [ ] Instancia de WhatsApp vinculada (QR escaneado)
- [ ] Workflow "Post-Trabajo" creado y probado
- [ ] Primer mensaje de prueba enviado exitosamente
- [ ] Monitoreo de logs configurado
- [ ] Documentación de workflows guardada

---

**Última actualización:** 6 de Diciembre 2025
**Autor:** Sistema de Automatización Chankas Car
