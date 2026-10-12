# Manual del administrador

Ingresa en `https://logistica24-7.cl/admin` con tu correo y contraseña. El menú de la izquierda tiene todas las secciones.

## Panel (inicio)
Muestra cuántos servicios, fotos y clientes están publicados, los mensajes sin leer y la lista de **textos provisionales pendientes** de confirmar. También avisa si el correo no está configurado.

## Textos del sitio
Elige la sección (Inicio, Nosotros, Ubicación, Contacto, Redes, Buscadores...) y edita los campos.
- Los textos con la etiqueta **Provisional** son de ejemplo. Al cambiarlos dejan de ser provisionales. Si quedan bien tal como están, marca **"Este texto ya es definitivo"**.
- **Valores corporativos**: uno por línea con el formato `Nombre del valor: explicación`.
- **Imágenes** (portada y Nosotros): elige el archivo, verás una vista previa; escribe el texto alternativo y guarda.
- **Contacto**: lo que dejes vacío no aparece en el sitio. Al cargar el WhatsApp aparece el botón flotante.
- **Galería / Clientes**: puedes ocultar toda la sección con el interruptor; desaparece del menú.

## Servicios
- **Nuevo servicio**: título, descripción, imagen (opcional) y texto alternativo.
- Las flechas ↑ ↓ cambian el orden en que se muestran.
- **Despublicar** oculta el servicio sin borrarlo; **Publicar** lo vuelve a mostrar.
- **Eliminar** lo borra definitivamente junto con su imagen.
- Puedes activar o quitar el botón "Solicitar información" de cada tarjeta.

## Galería
Agrega fotos con un título opcional. Se muestran con proporción uniforme y se pueden ampliar. Si no hay fotos, el sitio muestra un mensaje de "próximamente" (o puedes ocultar la sección).

## Clientes
Cada cliente necesita su **logotipo** y nombre; el sitio web es opcional. **Publica solo clientes que hayan autorizado el uso de su logo.** Un cliente sin logo no se muestra.

## Mensajes
Aquí quedan todas las consultas del formulario, aunque el correo falle. Los nuevos aparecen resaltados y con un contador en el menú.

## Mi cuenta
Cambia tu contraseña (mínimo 10 caracteres, con letras y números). Si la olvidas, quien administra el servidor puede restablecerla con `php bin/create-admin.php tu@correo.cl`.

## Recomendaciones para las imágenes
- Formatos: JPG, PNG o WebP, hasta 5 MB. El sitio las optimiza solo.
- Fotos horizontales de al menos 1200 px de ancho para portada y servicios; verticales para la sección Nosotros.
- Logos de clientes: PNG con fondo transparente.
- Usa fotos propias o con licencia. No publiques imágenes de terceros como si fueran de la empresa.
- Escribe un texto alternativo corto que describa lo que se ve (por ejemplo, "Camión de reparto cargando en bodega").
