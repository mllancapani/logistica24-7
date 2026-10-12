# Sitio web Veinticuatrosiete Logística SpA

Sitio corporativo (7 secciones) con panel de administración, hecho en **PHP 8 sin frameworks** y **MySQL/MariaDB**.
Dominio previsto: `logistica24-7.cl`.

## Qué incluye

- Sitio público: Inicio, Nosotros, Servicios, Galería, Clientes, Ubicación y Contacto, responsive, con SEO (títulos y descripciones por página, Open Graph, `sitemap.xml`, `robots.txt`, datos estructurados).
- Formulario de contacto real: validación en servidor, token CSRF, campo trampa, tiempo mínimo, límite por IP, respaldo en base de datos y envío por SMTP.
- Panel `/admin` en español: textos, servicios, galería, clientes, mensajes y cambio de contraseña.
- Subida segura de imágenes: se valida el contenido, se vuelven a codificar con GD (WebP), con miniatura y nombre aleatorio; la carpeta `uploads` no ejecuta código.
- Contenido provisional marcado como tal (en el panel y, mientras `SHOW_PROVISIONAL=true`, también en el sitio).

## Estructura

```
public/            Raíz web: index.php, assets/ (css, js, img), uploads/
app/
  Core/            Router, Db, Auth, Csrf, Images, Mailer, Settings, RateLimit...
  Controllers/     Sitio público y Admin/
  Views/           public/ y admin/
  Content.php      Define los textos editables del panel
  Vendor/PHPMailer Librería de correo incluida (sin Composer)
database/          schema.sql y seed.sql
bin/               install.php, create-admin.php, backup.php
storage/           logs y respaldos (no público)
docs/              Instalación, manual del administrador, seguridad y mantención
.env.example       Plantilla de configuración (copiar a .env)
```

## Desarrollo local

```bash
cp .env.example .env        # APP_ENV=local y datos de una base MySQL local
php bin/install.php         # crea tablas, contenido inicial y el administrador
php -S 127.0.0.1:8080 -t public public/index.php
```

## Documentación

- [Instalación y publicación](docs/INSTALACION.md)
- [Manual del administrador](docs/MANUAL-ADMINISTRADOR.md)
- [Seguridad, respaldos y mantención](docs/SEGURIDAD-Y-MANTENCION.md)

## Contenido pendiente de confirmar

| Dato | Estado |
|---|---|
| Razón social y RUT | Tomados de la cédula de RUT del SII |
| Dirección (Pasaje Interior 1 N° 112, Agrupación Cien, Punta Arenas) | Es la casa matriz del SII; confirmar que es la que se publica |
| Giro | La glosa del SII está cortada ("arriendo vehic"); confirmar |
| Textos de Inicio, Nosotros, Servicios | Provisionales |
| Fotografías, logos de clientes | Aún no hay; la galería y clientes muestran un mensaje "próximamente" |
| Teléfono, WhatsApp, correos, redes | Pendientes; lo vacío no se muestra |
| Datos SMTP | Pendientes (ver `docs/INSTALACION.md`) |

Los logotipos de clientes solo deben publicarse con autorización escrita de cada empresa.
