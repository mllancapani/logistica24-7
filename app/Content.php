<?php
declare(strict_types=1);

/**
 * Definición de los textos y datos editables desde el panel (Textos del sitio).
 * Tipos: text, textarea, url, tel, email, emails, check, image.
 * Para agregar un texto nuevo basta con sumarlo aquí y usar setting('clave') en la vista.
 */
$seo = [];
foreach (['inicio' => 'Inicio', 'nosotros' => 'Nosotros', 'servicios' => 'Servicios', 'galeria' => 'Galería', 'clientes' => 'Clientes', 'ubicacion' => 'Ubicación', 'contacto' => 'Contacto'] as $k => $label) {
    $seo[] = ['key' => "seo_{$k}_t", 'label' => "$label: título para buscadores", 'type' => 'text', 'max' => 70, 'help' => 'Ideal hasta 60 caracteres.'];
    $seo[] = ['key' => "seo_{$k}_d", 'label' => "$label: descripción para buscadores", 'type' => 'textarea', 'rows' => 2, 'max' => 170, 'help' => 'Ideal entre 120 y 160 caracteres.'];
}

return [
    'inicio' => ['title' => 'Inicio (portada)', 'intro' => 'Lo primero que ve el visitante.', 'fields' => [
        ['key' => 'ini_eyebrow', 'label' => 'Frase sobre el título', 'type' => 'text', 'max' => 80],
        ['key' => 'ini_titulo', 'label' => 'Título principal', 'type' => 'text', 'max' => 90, 'help' => 'Una frase corta que diga qué hace la empresa.'],
        ['key' => 'ini_texto', 'label' => 'Texto de presentación', 'type' => 'textarea', 'rows' => 3, 'max' => 400],
        ['key' => 'ini_btn1', 'label' => 'Botón principal', 'type' => 'text', 'max' => 40],
        ['key' => 'ini_btn2', 'label' => 'Botón secundario', 'type' => 'text', 'max' => 40],
        ['key' => 'ini_hero_img', 'label' => 'Imagen principal', 'type' => 'image', 'alt' => 'ini_hero_alt', 'help' => 'Foto horizontal o cuadrada, de buena calidad. Se optimiza automáticamente.'],
        ['key' => 'ini_slider_on', 'label' => 'Mostrar los servicios como diapositivas en la portada', 'type' => 'check', 'help' => 'La portada rota sola entre la presentación y hasta 4 servicios activos. Si la desactivas, queda solo la presentación.'],
        ['key' => 'ini_cta_titulo', 'label' => 'Banner inferior: título', 'type' => 'text', 'max' => 100],
        ['key' => 'ini_cta_texto', 'label' => 'Banner inferior: texto', 'type' => 'text', 'max' => 200],
    ]],
    'nosotros' => ['title' => 'Nosotros', 'intro' => 'Información corporativa.', 'fields' => [
        ['key' => 'nos_pres', 'label' => 'Presentación de la empresa', 'type' => 'textarea', 'rows' => 5, 'max' => 3000, 'help' => 'Deja una línea en blanco para separar párrafos.'],
        ['key' => 'nos_hist', 'label' => 'Historia o descripción institucional', 'type' => 'textarea', 'rows' => 5, 'max' => 3000],
        ['key' => 'nos_mis', 'label' => 'Misión', 'type' => 'textarea', 'rows' => 4, 'max' => 1500],
        ['key' => 'nos_vis', 'label' => 'Visión', 'type' => 'textarea', 'rows' => 4, 'max' => 1500],
        ['key' => 'nos_valores', 'label' => 'Valores corporativos', 'type' => 'textarea', 'rows' => 6, 'max' => 2000, 'help' => 'Uno por línea, con el formato: Nombre del valor: explicación.'],
        ['key' => 'nos_img', 'label' => 'Fotografía de la sección', 'type' => 'image', 'alt' => 'nos_img_alt', 'help' => 'Instalaciones, equipo u operaciones. Mejor en formato vertical.'],
    ]],
    'servicios' => ['title' => 'Servicios (introducción)', 'intro' => 'Los servicios se administran en la sección Servicios del menú.', 'fields' => [
        ['key' => 'serv_intro', 'label' => 'Introducción de la página Servicios', 'type' => 'textarea', 'rows' => 3, 'max' => 400],
    ]],
    'galeria' => ['title' => 'Galería (textos y visibilidad)', 'intro' => 'Las imágenes se administran en la sección Galería del menú.', 'fields' => [
        ['key' => 'sec_gallery_on', 'label' => 'Mostrar la galería en el sitio', 'type' => 'check', 'help' => 'Si la desactivas, desaparece del menú y de la web.'],
        ['key' => 'gal_intro', 'label' => 'Introducción', 'type' => 'text', 'max' => 200],
        ['key' => 'gal_empty', 'label' => 'Mensaje cuando aún no hay fotos', 'type' => 'text', 'max' => 200],
    ]],
    'clientes' => ['title' => 'Clientes (textos y visibilidad)', 'intro' => 'Los logotipos se administran en la sección Clientes del menú. Publica solo clientes que hayan autorizado el uso de su logo.', 'fields' => [
        ['key' => 'sec_clients_on', 'label' => 'Mostrar la sección Clientes en el sitio', 'type' => 'check', 'help' => 'Si la desactivas, desaparece del menú y de la web.'],
        ['key' => 'cli_intro', 'label' => 'Introducción', 'type' => 'text', 'max' => 200],
        ['key' => 'cli_empty', 'label' => 'Mensaje cuando aún no hay clientes publicados', 'type' => 'text', 'max' => 200],
    ]],
    'ubicacion' => ['title' => 'Ubicación', 'intro' => 'Dirección oficial que se muestra en el sitio y en el mapa.', 'fields' => [
        ['key' => 'ubi_dir', 'label' => 'Dirección', 'type' => 'text', 'max' => 200, 'help' => 'Calle, número y datos de la dirección.'],
        ['key' => 'ubi_com', 'label' => 'Comuna', 'type' => 'text', 'max' => 80],
        ['key' => 'ubi_ciu', 'label' => 'Ciudad', 'type' => 'text', 'max' => 80],
        ['key' => 'ubi_reg', 'label' => 'Región', 'type' => 'text', 'max' => 80],
        ['key' => 'ubi_hor', 'label' => 'Horario de atención (opcional)', 'type' => 'text', 'max' => 200],
        ['key' => 'ubi_ref', 'label' => 'Referencias para llegar (opcional)', 'type' => 'textarea', 'rows' => 3, 'max' => 500],
        ['key' => 'ubi_map', 'label' => 'Enlace de Google Maps (opcional)', 'type' => 'url', 'max' => 400, 'help' => 'En Google Maps busca el lugar, pulsa Compartir y copia el enlace. Si lo dejas vacío, se usa la dirección.'],
    ]],
    'contacto' => ['title' => 'Contacto', 'intro' => 'Los medios que dejes vacíos no se muestran en el sitio.', 'fields' => [
        ['key' => 'con_intro', 'label' => 'Texto de la página Contacto', 'type' => 'text', 'max' => 200],
        ['key' => 'con_tel', 'label' => 'Teléfono', 'type' => 'tel', 'max' => 30, 'help' => 'Con código de país, por ejemplo +56 61 2000000.'],
        ['key' => 'con_wa', 'label' => 'WhatsApp', 'type' => 'tel', 'max' => 30, 'help' => 'Con código de país, por ejemplo +56 9 1234 5678. Activa el botón flotante.'],
        ['key' => 'con_wa_msg', 'label' => 'Mensaje inicial de WhatsApp', 'type' => 'text', 'max' => 200],
        ['key' => 'con_mail', 'label' => 'Correo que se muestra en el sitio', 'type' => 'email', 'max' => 190],
        ['key' => 'con_dest', 'label' => 'Correo que recibe las consultas del formulario', 'type' => 'emails', 'max' => 300, 'help' => 'Si lo dejas vacío se usa el configurado en el servidor (MAIL_TO). Puedes poner varios separados por coma.'],
    ]],
    'redes' => ['title' => 'Redes sociales', 'intro' => 'Pega la dirección completa del perfil (comienza con https://).', 'fields' => [
        ['key' => 'con_ig', 'label' => 'Instagram', 'type' => 'url', 'max' => 300],
        ['key' => 'con_fb', 'label' => 'Facebook', 'type' => 'url', 'max' => 300],
        ['key' => 'con_li', 'label' => 'LinkedIn', 'type' => 'url', 'max' => 300],
        ['key' => 'con_otra', 'label' => 'Otra red (TikTok, YouTube, X)', 'type' => 'url', 'max' => 300],
    ]],
    'empresa' => ['title' => 'Datos de la empresa', 'intro' => 'Aparecen en el pie de página y en los datos para buscadores.', 'fields' => [
        ['key' => 'emp_razon', 'label' => 'Razón social', 'type' => 'text', 'max' => 140],
    ]],
    'seo' => ['title' => 'Buscadores (SEO)', 'intro' => 'Título y descripción que aparecen en Google y al compartir el sitio.', 'fields' => $seo],
];
