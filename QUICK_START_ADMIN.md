# 🚀 INICIO RÁPIDO - USUARIO ADMIN

## ⚡ 3 Pasos para comenzar

### 1️⃣ Iniciar la Aplicación
```bash
cd /home/rorschard/Documents/Java/Inforecicla
mvn spring-boot:run
```

### 2️⃣ Esperar el Mensaje de Confirmación
Busca en los logs:
```
✅ Usuario Admin creado exitosamente
📧 Email: admin@inforecicla.com
🔐 Contraseña: Admin@123456
```

### 3️⃣ Acceder al Sistema
1. Abre: `http://localhost:8080/login`
2. Ingresa:
   - **Email:** `admin@inforecicla.com`
   - **Contraseña:** `Admin@123456`
3. Haz clic en "Iniciar sesión"

---

## 📋 Credenciales de Acceso

| Campo | Valor |
|-------|-------|
| **Email** | admin@inforecicla.com |
| **Contraseña** | Admin@123456 |

---

## ✅ Verificación

Después de iniciar sesión, podrás:
- ✅ Acceder al dashboard
- ✅ Gestionar usuarios
- ✅ Acceder a panel de administración
- ✅ Cambiar contraseña

---

## 🔍 Si Algo Sale Mal

### No aparece el mensaje de creación
1. Revisa los logs de la consola
2. Verifica que `DataInitializer.java` existe en `src/main/java/org/sena/inforecicla/config/`
3. Ejecuta el script SQL manual: `create_admin_user.sql`

### No puedes iniciar sesión
1. Verifica el email: `admin@inforecicla.com` (sin espacios)
2. Verifica la contraseña: `Admin@123456` (mayúsculas importan)
3. Limpia cookies del navegador
4. Intenta en incógnito/privada

### Error de base de datos
1. Asegúrate de que MariaDB está corriendo
2. Verifica la conexión en `application.properties`
3. Confirma que la base de datos `inforecicla` existe

---

## 📚 Documentación Completa

Para más detalles, lee:
- `ADMIN_USER_GUIDE.md` - Guía completa del admin
- `RESUMEN_ADMIN_SETUP.md` - Resumen técnico
- `verify_admin_user.sql` - Scripts de verificación

---

## 🎯 Próximo Paso Recomendado

**Cambia la contraseña del admin:**

1. Inicia sesión como admin
2. Ve a tu perfil
3. Selecciona "Cambiar contraseña"
4. Usa una contraseña más segura
5. Guarda los cambios

---

**¡Listo! Tu sistema está configurado y listo para usar.** ✨

