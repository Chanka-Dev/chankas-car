# 🛡️ Guía de Configuración de Seguridad - Fase 1

## ✅ Medidas Implementadas

### 1. Fail2Ban ✓
**Estado**: Activo y funcionando

**Verificar estado**:
```bash
sudo fail2ban-client status laravel
sudo tail -f /var/log/fail2ban.log
```

**Configuración**:
- **Máximo intentos**: 5 en 10 minutos
- **Tiempo de baneo**: 1 hora
- **Log monitoreado**: `/var/www/chankascar/storage/logs/laravel.log`

**Desbanear una IP**:
```bash
sudo fail2ban-client set laravel unbanip 192.168.1.100
```

### 2. Google reCAPTCHA v3 ✓
**Estado**: Instalado, requiere configuración

**Pasos para activar**:

1. **Obtener claves de Google**:
   - Ir a: https://www.google.com/recaptcha/admin/create
   - Tipo: **reCAPTCHA v3**
   - Dominios: Tu dominio (ej: `chankascar.com`)
   - Copiar: **Site Key** y **Secret Key**

2. **Configurar variables de entorno**:
   ```bash
   nano .env
   ```
   
   Agregar:
   ```env
   RECAPTCHA_SITE_KEY=tu_site_key_aqui
   RECAPTCHA_SECRET_KEY=tu_secret_key_aqui
   SESSION_SECURE_COOKIE=true  # Solo si tienes HTTPS
   ```

3. **Agregar script en login/register views**:
   
   En `resources/views/auth/login.blade.php` y `register.blade.php`, agregar antes de `</head>`:
   ```html
   @if(config('services.recaptcha.site_key'))
   <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
   <script>
       grecaptcha.ready(function() {
           grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: 'login'})
               .then(function(token) {
                   document.querySelector('form').insertAdjacentHTML('beforeend', 
                       '<input type="hidden" name="g-recaptcha-response" value="' + token + '">');
               });
       });
   </script>
   @endif
   ```

4. **Limpiar caché**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

**Validación**: El middleware solo se activa en `production`. Para probar en local:
```bash
APP_ENV=production php artisan serve
```

### 3. Security Headers ✓
**Estado**: Activo globalmente

**Headers implementados**:
- ✅ `Strict-Transport-Security` (HSTS): Fuerza HTTPS por 1 año
- ✅ `X-Frame-Options`: Previene clickjacking
- ✅ `X-Content-Type-Options`: Previene MIME sniffing
- ✅ `X-XSS-Protection`: Protección XSS legacy
- ✅ `Referrer-Policy`: Control de referrer
- ✅ `Content-Security-Policy`: Política de contenido
- ✅ `Permissions-Policy`: Restringe APIs del navegador

**Verificar headers**:
```bash
curl -I https://tu-dominio.com | grep -E "X-|Strict|Content-Security"
```

**Probar en línea**:
- https://securityheaders.com
- https://observatory.mozilla.org

## 📋 Checklist Post-Implementación

- [ ] Fail2Ban activo: `sudo systemctl status fail2ban`
- [ ] Registrar cuenta en Google reCAPTCHA
- [ ] Agregar claves en `.env`
- [ ] Insertar scripts de reCAPTCHA en vistas de login/registro
- [ ] Verificar headers en producción
- [ ] Configurar `SESSION_SECURE_COOKIE=true` si tienes HTTPS
- [ ] Probar login con usuario incorrecto 6 veces (debe banear)
- [ ] Verificar score de reCAPTCHA en Google Admin Console
- [ ] Revisar logs: `tail -f storage/logs/laravel.log`

## 🚨 Monitoreo

### Logs de Fail2Ban
```bash
# Ver IPs baneadas
sudo fail2ban-client status laravel

# Ver log en tiempo real
sudo tail -f /var/log/fail2ban.log

# Ver intentos de login fallidos
tail -f /var/www/chankascar/storage/logs/laravel.log | grep "Failed login"
```

### Dashboard de reCAPTCHA
- URL: https://www.google.com/recaptcha/admin
- Métricas: Score promedio, solicitudes bloqueadas, tráfico por país

## 🔧 Troubleshooting

### Fail2Ban no banea
```bash
# Verificar que el filtro funciona
sudo fail2ban-regex /var/www/chankascar/storage/logs/laravel.log /etc/fail2ban/filter.d/laravel.conf

# Reiniciar servicio
sudo systemctl restart fail2ban
```

### reCAPTCHA muestra error
- Verificar que las claves están en `.env`
- Confirmar que el dominio está registrado en Google
- Revisar consola del navegador (F12)
- Verificar que `APP_ENV=production`

### Headers no aparecen
```bash
# Limpiar caché
php artisan config:clear
php artisan route:clear

# Verificar que el middleware está registrado
php artisan route:list | grep SecurityHeaders
```

## 📊 Métricas de Éxito

| Métrica | Objetivo | Herramienta |
|---------|----------|-------------|
| IPs baneadas/día | < 10 | `fail2ban-client status laravel` |
| Score reCAPTCHA | > 0.7 | Google reCAPTCHA Admin |
| Security Headers | Grado A | securityheaders.com |
| Login attempts/min | < 5 | Laravel logs |

## 🎯 Próximos Pasos (Fase 2)

1. **Obfuscar ruta de login**: Mover `/login` a `/panel/auth`
2. **Landing page pública**: Crear vista pública en `/`
3. **Cloudflare WAF**: Configurar reglas de firewall
4. **Rate limiting avanzado**: Límites por IP + Usuario
5. **2FA opcional**: Autenticación de dos factores para admin

---

**Última actualización**: 4 de diciembre de 2025
**Versión**: Fase 1 - Security Express
