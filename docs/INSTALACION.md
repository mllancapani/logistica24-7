# Instalación y publicación

## 1. Requisitos del hosting

- PHP 8.1 o superior con extensiones: `pdo_mysql`, `gd` (con soporte WebP), `mbstring`, `fileinfo`, `openssl`. Recomendadas: `zip`, `exif`.
- MySQL 5.7+ o MariaDB 10.3+.
- Apache con `mod_rewrite` (usa `public/.htaccess`). En IIS se incluye `public/web.config`.
- Certificado HTTPS (AutoSSL / Let's Encrypt).
- Acceso a una terminal (SSH o "Terminal" de cPanel) para ejecutar `php bin/install.php`. Si no hay terminal, ver el apartado 6.

## 2. Base de datos

En el panel del hosting crea una base de datos y un usuario con todos los permisos sobre ella. Anota nombre, usuario y contraseña.

## 3. Subir los archivos

El código está pensado para que **solo la carpeta `public/` sea accesible desde internet**.

**Opción A: el hosting permite elegir la carpeta raíz del dominio.** Sube todo el proyecto y apunta la raíz del dominio a `.../logistica24-7/public`. No hay que editar nada.

**Opción B: cPanel clásico (raíz fija `public_html`).**
1. Sube todo **excepto `public/`** a una carpeta fuera de `public_html`, por ejemplo `/home/USUARIO/logistica24-7/`.
2. Sube el **contenido** de `public/` (incluidos los archivos ocultos `.htaccess`) a `public_html/`.
3. Edita `public_html/index.php` y cambia la línea `$app = dirname(__DIR__);` por la ruta real:
   `$app = '/home/USUARIO/logistica24-7';`

## 4. Configuración

```bash
cd /home/USUARIO/logistica24-7
cp .env.example .env
nano .env          # completa los valores
chmod 640 .env
```

Valores importantes:

| Variable | Qué poner |
|---|---|
| `APP_ENV` | `production` |
| `APP_URL` | `https://logistica24-7.cl` |
| `DB_*` | Datos de la base creada en el paso 2 |
| `MAIL_HOST` / `MAIL_PORT` / `MAIL_SECURE` | Servidor SMTP del correo corporativo. Ej.: `mail.logistica24-7.cl`, `465`, `ssl` (o `587`, `tls`) |
| `MAIL_USER` / `MAIL_PASS` | Cuenta de correo que envía (ej. `no-responder@logistica24-7.cl`) |
| `MAIL_FROM` | Debe ser una dirección del propio dominio (mejora la entrega; configura SPF y DKIM en el DNS) |
| `MAIL_TO` | Correo que recibe las consultas. También se puede cambiar desde el panel (Textos > Contacto) |
| `SHOW_PROVISIONAL` | `true` mientras haya textos provisionales; `false` al publicar con contenido definitivo |

Permisos de escritura necesarios: `public/uploads/` y `storage/` (normalmente 755; 775 si el hosting lo exige).

## 5. Crear tablas y el usuario administrador

```bash
php bin/install.php
```

El instalador crea las tablas, carga el contenido inicial y **pide el correo y la contraseña del primer administrador** (mínimo 10 caracteres, letras y números). No existen contraseñas por defecto. Es seguro repetirlo: no borra datos.

Para crear otro administrador o restablecer una contraseña olvidada:

```bash
php bin/create-admin.php correo@dominio.cl
```

## 6. Si no hay terminal

1. En phpMyAdmin importa `database/schema.sql` y luego `database/seed.sql`.
2. En tu computador genera el hash de tu contraseña: `php -r 'echo password_hash("TuClaveSegura123", PASSWORD_DEFAULT);'`
3. En phpMyAdmin ejecuta:
   `INSERT INTO users (email, name, password_hash, created_at) VALUES ('tu@correo.cl', 'Tu nombre', 'HASH_GENERADO', NOW());`

## 7. HTTPS

1. Activa el certificado (AutoSSL / Let's Encrypt) en el hosting.
2. En `public/.htaccess` descomenta las tres líneas de "Forzar HTTPS".
3. Verifica que `APP_URL` empieza con `https://`.

## 8. Lista de verificación antes de publicar

- [ ] Entrar a `https://logistica24-7.cl/admin` e iniciar sesión.
- [ ] Enviar un mensaje desde el formulario de contacto y comprobar que llega al correo y aparece en Mensajes.
- [ ] Completar o confirmar los textos pendientes (el Panel los lista).
- [ ] Cargar teléfono, WhatsApp, correo, redes y dirección oficiales.
- [ ] Subir fotografías reales de la empresa; publicar logos solo de clientes autorizados.
- [ ] Poner `SHOW_PROVISIONAL=false` en `.env`.
- [ ] Revisar `https://logistica24-7.cl/sitemap.xml` y registrar el sitio en Google Search Console.
- [ ] Programar el respaldo diario (ver `SEGURIDAD-Y-MANTENCION.md`).
- [ ] Probar en celular, tablet y computador.

## 9. Solución de problemas

| Síntoma | Causa probable |
|---|---|
| Error 500 | Revisa `storage/logs/php-error.log`. Suele ser un dato incorrecto en `.env` o una extensión PHP faltante |
| Todas las páginas internas dan 404 | `mod_rewrite` o `.htaccess` no se subió (es un archivo oculto) |
| No se pueden subir imágenes | Falta permiso de escritura en `public/uploads/` o falta la extensión `gd` |
| El formulario guarda pero no llega el correo | Revisa los datos SMTP; el motivo queda en Mensajes (detalle del mensaje) y en el log |
| El mapa no se ve | Verifica que la dirección esté cargada en Textos > Ubicación |
