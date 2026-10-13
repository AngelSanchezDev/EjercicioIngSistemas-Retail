-- Esquema: Sistema de Gestión de Inventarios ModaExpress
-- Motor: MySQL 8 / MariaDB 10.4+

CREATE DATABASE IF NOT EXISTS moda_express CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE moda_express;

-- Roles de usuario: admin (gerencia general), gerente (gerente de tienda), vendedor
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('admin','gerente','vendedor') NOT NULL DEFAULT 'vendedor',
    tienda_id INT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tiendas físicas + canal online (tipo='online')
CREATE TABLE tiendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(200) NULL,
    tipo ENUM('fisica','online') NOT NULL DEFAULT 'fisica',
    activo TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

ALTER TABLE usuarios ADD CONSTRAINT fk_usuarios_tienda FOREIGN KEY (tienda_id) REFERENCES tiendas(id) ON DELETE SET NULL;

-- Categorías de producto
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Catálogo de productos
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) NOT NULL UNIQUE COMMENT 'Código de barras / SKU',
    nombre VARCHAR(150) NOT NULL,
    categoria_id INT NULL,
    precio DECIMAL(10,2) NOT NULL DEFAULT 0,
    stock_minimo INT NOT NULL DEFAULT 10 COMMENT 'Umbral de alerta por bajo stock',
    stock_maximo INT NOT NULL DEFAULT 200 COMMENT 'Umbral de alerta por sobrestock',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Stock de cada producto por tienda (incluye la tienda "Online")
CREATE TABLE stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    tienda_id INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 0,
    UNIQUE KEY uq_producto_tienda (producto_id, tienda_id),
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (tienda_id) REFERENCES tiendas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Historial de movimientos de inventario (entrada, salida, venta, transferencia, ajuste)
CREATE TABLE movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    tienda_id INT NOT NULL,
    tipo ENUM('entrada','salida','venta','ajuste','transferencia_salida','transferencia_entrada') NOT NULL,
    cantidad INT NOT NULL,
    usuario_id INT NULL,
    referencia VARCHAR(100) NULL COMMENT 'N° de boleta/orden/canal',
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (tienda_id) REFERENCES tiendas(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_mov_creado (creado_en)
) ENGINE=InnoDB;

-- Solicitudes de reposición (flujo BPMN de aprobación)
CREATE TABLE reposiciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    tienda_id INT NOT NULL,
    cantidad_solicitada INT NOT NULL,
    estado ENUM('pendiente','aprobada','rechazada','recibida') NOT NULL DEFAULT 'pendiente',
    solicitado_por INT NULL,
    aprobado_por INT NULL,
    comentario VARCHAR(255) NULL,
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    resuelto_en DATETIME NULL,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (tienda_id) REFERENCES tiendas(id) ON DELETE CASCADE,
    FOREIGN KEY (solicitado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    FOREIGN KEY (aprobado_por) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;
