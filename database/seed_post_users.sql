-- Ejecutar DESPUÉS de database/seed.php (necesita que existan los usuarios).
-- Orden completo: schema.sql -> seed.sql -> seed.php -> seed_post_users.sql
USE moda_express;

INSERT INTO movimientos (producto_id, tienda_id, tipo, cantidad, usuario_id, referencia) VALUES
(1, 1, 'entrada', 50, 1, 'OC-1001'),
(1, 1, 'venta', 5, 3, 'BOL-0001'),
(3, 1, 'venta', 2, 3, 'BOL-0002'),
(6, 3, 'venta', 8, NULL, 'BOL-0003'),
(7, 6, 'venta', 20, NULL, 'WEB-00045');

-- Una solicitud de reposición pendiente (para probar el flujo BPMN)
INSERT INTO reposiciones (producto_id, tienda_id, cantidad_solicitada, estado, solicitado_por, comentario) VALUES
(6, 3, 40, 'pendiente', 2, 'Stock crítico, alta demanda por temporada');
