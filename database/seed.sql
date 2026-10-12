-- Contenido inicial. is_draft = 1 marca textos provisionales pendientes de confirmar.
-- Los datos de razón social y RUT provienen de la cédula de RUT del SII.
SET NAMES utf8mb4;

INSERT IGNORE INTO settings (k, v, is_draft) VALUES
('emp_razon', 'Veinticuatrosiete Logística SpA', 0),
('emp_rut', '77.805.583-K', 0),
('site_name', 'Veinticuatrosiete', 0),

('ini_eyebrow', 'Apoyo logístico a empresas', 1),
('ini_titulo', 'Soluciones logísticas para tu empresa', 1),
('ini_texto', 'Texto provisional. Aquí irá una presentación breve de Veinticuatrosiete Logística SpA y de cómo apoya a sus clientes.', 1),
('ini_btn1', 'Solicitar cotización', 0),
('ini_btn2', 'Conocer servicios', 0),
('ini_cta_titulo', '¿Necesitas apoyo logístico para tu empresa?', 1),
('ini_cta_texto', 'Cuéntanos qué necesitas y te responderemos a la brevedad.', 1),

('nos_pres', 'Texto provisional. Aquí irá la presentación de la empresa.', 1),
('nos_hist', 'Texto provisional. Aquí irá la historia o descripción institucional.', 1),
('nos_mis', 'Texto provisional. Aquí irá la misión de la empresa.', 1),
('nos_vis', 'Texto provisional. Aquí irá la visión de la empresa.', 1),
('nos_valores', 'Valor 1: Descripción pendiente de confirmar.\nValor 2: Descripción pendiente de confirmar.\nValor 3: Descripción pendiente de confirmar.', 1),

('serv_intro', 'Texto provisional. Aquí irá una introducción a los servicios de la empresa.', 1),

('sec_gallery_on', '1', 0),
('gal_intro', 'Vehículos, operaciones e instalaciones de la empresa.', 1),
('gal_empty', 'Próximamente incorporaremos fotografías de nuestras operaciones.', 0),

('sec_clients_on', '1', 0),
('cli_intro', 'Empresas con las que trabajamos.', 1),
('cli_empty', 'Próximamente publicaremos las empresas con las que trabajamos.', 0),

('ubi_dir', 'Pasaje Interior 1 N° 112, Agrupación Cien', 1),
('ubi_com', 'Punta Arenas', 0),
('ubi_ciu', 'Punta Arenas', 0),
('ubi_reg', 'Magallanes y de la Antártica Chilena', 0),
('ubi_hor', '', 0),
('ubi_ref', '', 0),
('ubi_map', '', 0),

('con_intro', 'Escríbenos y te responderemos a la brevedad.', 1),
('con_tel', '', 0),
('con_wa', '', 0),
('con_wa_msg', 'Hola, quiero solicitar información sobre sus servicios.', 0),
('con_mail', '', 0),
('con_dest', '', 0),
('con_ig', '', 0),
('con_fb', '', 0),
('con_li', '', 0),
('con_otra', '', 0),

('seo_inicio_t', 'Veinticuatrosiete Logística SpA | Apoyo logístico a empresas', 1),
('seo_inicio_d', 'Veinticuatrosiete Logística SpA: apoyo logístico a empresas en Punta Arenas. Solicita tu cotización.', 1),
('seo_nosotros_t', 'Nosotros | Veinticuatrosiete Logística SpA', 1),
('seo_nosotros_d', 'Conoce a Veinticuatrosiete Logística SpA: quiénes somos, misión, visión y valores.', 1),
('seo_servicios_t', 'Servicios | Veinticuatrosiete Logística SpA', 1),
('seo_servicios_d', 'Servicios de apoyo logístico a empresas de Veinticuatrosiete Logística SpA.', 1),
('seo_galeria_t', 'Galería | Veinticuatrosiete Logística SpA', 1),
('seo_galeria_d', 'Fotografías de las operaciones e instalaciones de Veinticuatrosiete Logística SpA.', 1),
('seo_clientes_t', 'Nuestros clientes | Veinticuatrosiete Logística SpA', 1),
('seo_clientes_d', 'Empresas que trabajan con Veinticuatrosiete Logística SpA.', 1),
('seo_ubicacion_t', 'Ubicación | Veinticuatrosiete Logística SpA', 1),
('seo_ubicacion_d', 'Dónde encontrarnos en Punta Arenas y cómo llegar a Veinticuatrosiete Logística SpA.', 1),
('seo_contacto_t', 'Contacto y cotizaciones | Veinticuatrosiete Logística SpA', 1),
('seo_contacto_d', 'Contacta a Veinticuatrosiete Logística SpA y solicita una cotización.', 1);

INSERT INTO services (title, description, image_alt, show_cta, is_draft, sort_order, active, created_at)
SELECT * FROM (
  SELECT 'Apoyo logístico a empresas' AS title, 'Descripción provisional. Pendiente de confirmar con la empresa.' AS description, '' AS image_alt, 1 AS show_cta, 1 AS is_draft, 1 AS sort_order, 1 AS active, NOW() AS created_at
  UNION ALL SELECT 'Servicio por definir', 'Descripción provisional. Pendiente de confirmar con la empresa.', '', 1, 1, 2, 1, NOW()
  UNION ALL SELECT 'Servicio por definir', 'Descripción provisional. Pendiente de confirmar con la empresa.', '', 1, 1, 3, 1, NOW()
) AS s WHERE NOT EXISTS (SELECT 1 FROM services LIMIT 1);
