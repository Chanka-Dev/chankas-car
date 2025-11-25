# 🚀 Guía de Despliegue a GitHub

**Autor**: Pedro Antonio López Chumacero - Chanka's Development Team

## ℹ️ Información Importante

Tu proyecto actualmente está corriendo en tu servidor en `/var/www/chankascar`. Para subirlo a GitHub:

1. **SÍ debes subir el código** - GitHub será tu respaldo y control de versiones
2. **NO subirás archivos sensibles** - El `.gitignore` ya está configurado
3. **Tu servidor seguirá funcionando** - Git no afecta los archivos en producción

## 🔒 Archivos que NO se subirán (ya están en .gitignore)

- `.env` - ⚠️ NUNCA subir (contiene credenciales)
- `vendor/` - Dependencias de Composer (se reinstalan)
- `node_modules/` - Dependencias de NPM (se reinstalan)
- `storage/` - Archivos temporales y backups
- `/public/build` - Assets compilados (se regeneran)

## 📋 Pasos para Subir a GitHub

### 1. Preparar el Repositorio Local (en tu servidor)

```bash
cd /var/www/chankascar

# Verificar que git esté inicializado
git status

# Si dice "not a git repository", inicializar:
git init
```

### 2. Configurar Git (primera vez)

```bash
git config user.name "Pedro Antonio López Chumacero"
git config user.email "tu-email@gmail.com"  # Usa tu email de GitHub
```

### 3. Revisar qué archivos se subirán

```bash
# Ver qué archivos serán incluidos
git status

# Asegurarte que NO aparezcan:
# - .env
# - vendor/
# - node_modules/
# Si aparecen, verifica que .gitignore esté bien
```

### 4. Agregar archivos al staging

```bash
# Agregar todos los archivos (respetando .gitignore)
git add .

# Verificar qué se agregó
git status
```

### 5. Hacer el primer commit

```bash
git commit -m "🎉 Initial commit - Chankas Car v1.0.0

Sistema completo de gestión de taller GNV desarrollado con Laravel 11

Features principales:
- Sistema de autenticación con roles (Admin, Cajero, Técnico)
- Gestión de trabajos, empleados, clientes
- Control de inventario y proveedores
- Sistema de pagos a técnicos
- Generación de PDFs con diseño corporativo
- Sistema de auditoría avanzado
- Login moderno personalizado
- Tema con paleta Chankas Car
- Sistema de backups automáticos

Desarrollado por: Pedro Antonio López Chumacero
Chanka's Development Team - Cochabamba, Bolivia"
```

### 6. Crear Repositorio en GitHub

1. Ve a https://github.com
2. Inicia sesión con tu cuenta
3. Click en el botón **"+"** (arriba derecha) → **"New repository"**
4. Configurar:
   - **Repository name**: `chankascar` o `sistema-chankas-car`
   - **Description**: "Sistema de gestión para taller de conversión a GNV - Laravel 11"
   - **Visibility**: 
     - ✅ **Private** (recomendado - solo tú lo ves)
     - ⚠️ Public (todos pueden verlo)
   - **NO marques**: "Add a README" (ya tienes uno)
   - **NO marques**: "Add .gitignore" (ya tienes uno)
5. Click **"Create repository"**

### 7. Conectar tu Servidor con GitHub

GitHub te mostrará comandos. Usa estos:

```bash
cd /var/www/chankascar

# Agregar el remote (reemplaza TU-USUARIO con tu usuario de GitHub)
git remote add origin https://github.com/TU-USUARIO/chankascar.git

# Verificar que se agregó correctamente
git remote -v
```

### 8. Subir el Código

```bash
# Renombrar la rama a 'main' (estándar actual)
git branch -M main

# Subir todo a GitHub
git push -u origin main
```

**Si pide autenticación:**
- Usuario: tu usuario de GitHub
- Contraseña: **NO uses tu contraseña**, usa un **Personal Access Token**

### 9. Crear Personal Access Token (si no tienes)

1. En GitHub: Settings → Developer settings → Personal access tokens → Tokens (classic)
2. Click **"Generate new token"** → **"Generate new token (classic)"**
3. Configurar:
   - **Note**: "Chankas Car - Servidor Producción"
   - **Expiration**: 90 días (o lo que prefieras)
   - **Scopes**: Marcar solo `repo` (acceso completo a repositorios)
4. Click **"Generate token"**
5. **⚠️ COPIAR EL TOKEN** (solo se muestra una vez)
6. Usarlo como contraseña al hacer `git push`

### 10. Guardar Credenciales (Opcional)

Para no escribir usuario/token cada vez:

```bash
# Guardar credenciales por 1 hora
git config credential.helper 'cache --timeout=3600'

# O guardar permanentemente (menos seguro)
git config credential.helper store
```

## ✅ Verificación

Después de `git push`, ve a tu repositorio en GitHub y verifica:

- ✅ Archivos visibles en GitHub
- ✅ README.md se ve correctamente
- ✅ NO aparece carpeta `vendor/`
- ✅ NO aparece carpeta `node_modules/`
- ✅ NO aparece archivo `.env`

## 🔄 Flujo de Trabajo Futuro

### Cuando hagas cambios en tu servidor:

```bash
cd /var/www/chankascar

# 1. Ver qué cambió
git status

# 2. Agregar cambios
git add .

# 3. Commit descriptivo
git commit -m "✨ feat: agregar sistema de cotizaciones"

# 4. Subir a GitHub
git push
```

### Tipos de commits recomendados:

```bash
git commit -m "✨ feat: nueva característica"
git commit -m "🐛 fix: corrección de bug"
git commit -m "📝 docs: actualizar documentación"
git commit -m "♻️ refactor: refactorizar código"
git commit -m "🎨 style: cambios de estilo/diseño"
git commit -m "⚡ perf: mejora de rendimiento"
git commit -m "🔒 security: mejora de seguridad"
```

## 🎯 Crear Release v1.0.0 en GitHub

Después de subir el código:

1. En GitHub, ir a tu repositorio
2. Click en **"Releases"** (lado derecho)
3. Click **"Create a new release"**
4. Llenar:
   - **Choose a tag**: Escribir `v1.0.0` → "Create new tag"
   - **Release title**: `🚗 Chankas Car v1.0.0 - Release Inicial`
   - **Description**: Copiar desde CHANGELOG.md (sección [1.0.0])
   - **Set as the latest release**: ✅ Marcar
5. Click **"Publish release"**

## 📝 Archivo .env.example

GitHub no subirá tu `.env`, pero sí debes crear un `.env.example` de muestra:

```bash
cd /var/www/chankascar
cp .env .env.example

# Editar .env.example y reemplazar datos sensibles por ejemplos
nano .env.example
```

Cambiar en `.env.example`:
```env
DB_PASSWORD=tu_contraseña_aqui  →  DB_PASSWORD=password
MAIL_PASSWORD=xxx               →  MAIL_PASSWORD=tu_password_smtp
```

Luego agregar a git:
```bash
git add .env.example
git commit -m "📝 docs: agregar .env.example"
git push
```

## ⚠️ Importante

1. **Nunca** hagas `git add .env` (está protegido por .gitignore)
2. **Nunca** subas credenciales de base de datos
3. **Nunca** compartas tu Personal Access Token
4. GitHub es tu **backup** - haz push regularmente
5. Tu servidor seguirá funcionando normal, git no lo afecta

## 🆘 Solución de Problemas

### Error: "remote origin already exists"
```bash
git remote remove origin
git remote add origin https://github.com/TU-USUARIO/chankascar.git
```

### Error: "permission denied"
Verifica tu Personal Access Token o credenciales.

### No puedo hacer push
```bash
git pull origin main --allow-unrelated-histories
git push -u origin main
```

## 📞 Contacto

**Desarrollador**: Pedro Antonio López Chumacero  
**Equipo**: Chanka's Development Team  
**Ubicación**: Cochabamba, Bolivia

---

¿Dudas? Revisa la documentación de Git: https://git-scm.com/doc
