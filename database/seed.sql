-- Datos de ejemplo para ModaExpress
USE moda_express;

INSERT INTO tiendas (nombre, direccion, tipo) VALUES
('Tienda San Isidro', 'Av. Javier Prado 1200, Lima', 'fisica'),
('Tienda Miraflores', 'Av. Larco 345, Lima', 'fisica'),
('Tienda Surco', 'Av. Caminos del Inca 500, Lima', 'fisica'),
('Tienda La Molina', 'Av. La Molina 220, Lima', 'fisica'),
('Tienda Callao', 'Av. Sáenz Peña 88, Callao', 'fisica'),
('Canal Online', NULL, 'online');

INSERT INTO categorias (nombre) VALUES ('Camisas'), ('Pantalones'), ('Vestidos'), ('Calzado'), ('Accesorios');

-- Los usuarios NO se insertan aquí: se crean con database/seed.php
-- para generar el hash bcrypt real con password_hash() de PHP.
-- Contraseña para todos los usuarios de prueba: 123456

INSERT INTO productos (sku, nombre, categoria_id, precio, stock_minimo, stock_maximo) VALUES
('7750001000011', 'Camisa Oxford Celeste Talla M', 1, 89.90, 15, 150),
('7750001000028', 'Camisa Lino Blanca Talla L', 1, 99.90, 15, 150),
('7750002000035', 'Pantalón Chino Beige Talla 32', 2, 119.90, 10, 120),
('7750002000042', 'Pantalón Jean Slim Azul Talla 30', 2, 129.90, 10, 120),
('7750003000059', 'Vestido Floral Verano', 3, 149.90, 8, 80),
('7750004000066', 'Zapato Casual Cuero Marrón 42', 4, 189.90, 5, 60),
('7750004000073', 'Zapatilla Urbana Blanca 41', 4, 169.90, 5, 60),
('7750005000080', 'Cinturón de Cuero Negro', 5, 49.90, 20, 200),
('7750005000097', 'Bufanda de Lana Gris', 5, 39.90, 20, 200),
('7750003000066', 'Vestido Casual Negro', 3, 139.90, 8, 80);

-- Stock inicial por tienda (incluye Online = tienda 6)
INSERT INTO stock (producto_id, tienda_id, cantidad)
SELECT p.id, t.id,
  CASE
    WHEN t.id = 6 THEN FLOOR(5 + RAND()*20)
    ELSE FLOOR(10 + RAND()*60)
  END
FROM productos p
JOIN tiendas t;

-- Forzamos algunos casos de alerta: bajo stock y sobrestock
UPDATE stock SET cantidad = 3 WHERE producto_id = 5 AND tienda_id = 1;   -- bajo stock
UPDATE stock SET cantidad = 2 WHERE producto_id = 6 AND tienda_id = 3;   -- bajo stock
UPDATE stock SET cantidad = 250 WHERE producto_id = 8 AND tienda_id = 2; -- sobrestock
UPDATE stock SET cantidad = 0 WHERE producto_id = 7 AND tienda_id = 6;   -- quiebre de stock online

-- Algunos movimientos de ejemplo (historial)
INSERT INTO movimientos (producto_id, tienda_id, tipo, cantidad, usuario_id, referencia) VALUES
(1, 1, 'entrada', 50, 1, 'OC-1001'),
(1, 1, 'venta', 5, 3, 'BOL-0001'),
(3, 1, 'venta', 2, 3, 'BOL-0002'),
(6, 3, 'venta', 8, NULL, 'BOL-0003'),
(7, 6, 'venta', 20, NULL, 'WEB-00045');

-- Una solicitud de reposición pendiente (para probar el flujo BPMN)
INSERT INTO reposiciones (producto_id, tienda_id, cantidad_solicitada, estado, solicitado_por, comentario) VALUES
(6, 3, 40, 'pendiente', 2, 'Stock crítico, alta demanda por temporada');
