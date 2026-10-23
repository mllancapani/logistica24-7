-- Servicios informados por el cliente (descripciones propuestas, quedan marcadas como provisorias).
-- Ejecutar sobre la base "logistica24-7". Solo agrega; no borra nada.
SET NAMES utf8mb4;
SET @o = (SELECT COALESCE(MAX(sort_order), 0) FROM services);
INSERT INTO services (title, description, media_id, image_alt, show_cta, is_draft, sort_order, active, created_at) VALUES
('Transporte de pasajeros', 'Traslado de personas para empresas y equipos de trabajo, con puntualidad y atención cercana.', NULL, '', 1, 1, @o+1, 1, NOW()),
('Retiro y traslado de carga', 'Retiramos y trasladamos tu carga hasta el destino que necesites, coordinando cada etapa del servicio.', NULL, '', 1, 1, @o+2, 1, NOW()),
('Arriendo de camionetas', 'Camionetas disponibles para la operación diaria de tu empresa, por el tiempo que necesites.', NULL, '', 1, 1, @o+3, 1, NOW()),
('Gestión de compra de insumos', 'Nos encargamos de cotizar y comprar materias primas, repuestos y equipos para que tu equipo se concentre en su operación.', NULL, '', 1, 1, @o+4, 1, NOW()),
('Arriendo de bodega seca', 'Espacio para almacenar productos que no requieren frío, con acceso coordinado.', NULL, '', 1, 1, @o+5, 1, NOW()),
('Arriendo de bodega congelada', 'Almacenamiento en frío para productos que necesitan mantenerse congelados.', NULL, '', 1, 1, @o+6, 1, NOW()),
('Gestiones de apoyo a empresas', 'Realizamos trámites y gestiones de apoyo según lo que tu empresa necesite.', NULL, '', 1, 1, @o+7, 1, NOW());
