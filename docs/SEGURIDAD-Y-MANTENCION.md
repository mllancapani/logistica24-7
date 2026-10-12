# Seguridad, respaldos y mantención

## Medidas ya implementadas
- Contraseñas con `password_hash` (bcrypt/Argon2 según PHP), con rehash automático.
- Sesiones con cookie HttpOnly, SameSite=Lax y Secure en HTTPS; ID regenerado al ingresar; cierre por 2 horas sin actividad.
- Límite de intentos de acceso (por correo y por IP) y del formulario de contacto (por IP).
- Consultas SQL parametrizadas (PDO) y escape de toda salida HTML.
- Token CSRF en todos los formularios y acciones del panel; las acciones de borrar o cambiar estado son solo POST.
- Subidas de imágenes: validación por contenido real, límite de tamaño y dimensiones, re-codificación con GD (elimina metadatos y código incrustado), nombres aleatorios, sin SVG, y `uploads/` sin ejecución de scripts.
- Cabeceras de seguridad (CSP, X-Frame-Options, nosniff, Referrer-Policy, HSTS en HTTPS).
- Credenciales solo en `.env`, fuera de la carpeta pública; carpetas `app`, `bin`, `database` y `storage` bloqueadas por `.htaccess` en caso de quedar expuestas.
- El panel está excluido de buscadores (`robots.txt` y `noindex`).
- Errores internos no muestran detalles al visitante en producción.

## Recomendaciones
1. Mantén PHP en una versión con soporte vigente (pide al hosting actualizar cuando corresponda).
2. Usa contraseñas largas y únicas. Crea un segundo administrador de respaldo y elimina cuentas que ya no se usen.
3. No compartas el archivo `.env` ni lo subas a repositorios o correos.
4. Cambia la clave SMTP si sospechas que se filtró; usa una cuenta de correo exclusiva para el sitio.
5. Configura SPF y DKIM del dominio para que los correos del formulario no caigan en spam.
6. Revisa de vez en cuando `storage/logs/php-error.log`.
7. Si el formulario recibe mucho spam, se puede agregar Cloudflare Turnstile o reCAPTCHA (requiere una clave del proveedor).
8. Considera un monitor de disponibilidad gratuito (UptimeRobot u otro) para saber si el sitio cae.

## Respaldos
El script `bin/backup.php` genera `storage/backups/AAAA-MM-DD_HHMM/` con `base_de_datos.sql` y `imagenes.zip`, y conserva los últimos 14.

Programa una tarea cron diaria (cPanel > Tareas Cron):

```
0 3 * * * /usr/bin/php /home/USUARIO/logistica24-7/bin/backup.php >/dev/null 2>&1
```

**Descarga los respaldos periódicamente a otro lugar** (tu computador o un servicio en la nube): un respaldo que está en el mismo servidor no protege si el servidor falla. Haz además un respaldo completo antes de cada actualización.

### Restaurar
1. Importa `base_de_datos.sql` en la base de datos (phpMyAdmin o `mysql base < archivo.sql`).
2. Descomprime `imagenes.zip` dentro de `public/uploads/`.

## Actualizar el sitio
1. Haz un respaldo.
2. Sube los archivos nuevos sin pisar `.env` ni `public/uploads/`.
3. Ejecuta `php bin/install.php` (no borra datos) si se agregaron tablas o textos iniciales.
