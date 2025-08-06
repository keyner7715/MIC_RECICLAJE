-- 1. Tipos de Material
CREATE TABLE tipos_material (
    id_tipo_material INT AUTO_INCREMENT PRIMARY KEY,
    nombre_tipo VARCHAR(100) NOT NULL
);

-- 2. Materiales Reciclables
CREATE TABLE materiales (
    id_material INT AUTO_INCREMENT PRIMARY KEY,
    nombre_material VARCHAR(100) NOT NULL,
    descripcion TEXT,
    id_tipo_material INT,
    FOREIGN KEY (id_tipo_material) REFERENCES tipos_material(id_tipo_material)
);

-- 3. Clientes que entregan residuos
CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre_cliente VARCHAR(100) NOT NULL,
    cedula_ruc VARCHAR(20) UNIQUE NOT NULL,
    direccion VARCHAR(150),
    telefono VARCHAR(20),
    correo VARCHAR(100)
);

-- 4. Empleados que gestionan operaciones
CREATE TABLE empleados (
    id_empleado INT AUTO_INCREMENT PRIMARY KEY,
    nombre_empleado VARCHAR(100) NOT NULL,
    cargo VARCHAR(50),
    telefono VARCHAR(20),
    correo VARCHAR(100)
);

-- 5. Centros de acopio
CREATE TABLE centros_acopio (
    id_centro INT AUTO_INCREMENT PRIMARY KEY,
    nombre_centro VARCHAR(100),
    direccion VARCHAR(150),
    id_responsable INT,
    FOREIGN KEY (id_responsable) REFERENCES empleados(id_empleado)
);

-- 6. Recolecciones realizadas por clientes
CREATE TABLE recolecciones (
    id_recoleccion INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    id_cliente INT,
    id_empleado INT,
    id_centro INT,
    observaciones TEXT,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente),
    FOREIGN KEY (id_empleado) REFERENCES empleados(id_empleado),
    FOREIGN KEY (id_centro) REFERENCES centros_acopio(id_centro)
);

-- 7. Detalle de materiales reciclados por recolección
CREATE TABLE detalle_recoleccion (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_recoleccion INT,
    id_material INT,
    cantidad_kg DECIMAL(10,2),
    FOREIGN KEY (id_recoleccion) REFERENCES recolecciones(id_recoleccion),
    FOREIGN KEY (id_material) REFERENCES materiales(id_material)
);

-- 8. Proveedores de residuos industriales
CREATE TABLE proveedores (
    id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
    nombre_proveedor VARCHAR(100),
    tipo_proveedor VARCHAR(50),
    direccion VARCHAR(150),
    telefono VARCHAR(20),
    correo VARCHAR(100)
);

-- 9. Recolecciones realizadas a proveedores
CREATE TABLE recoleccion_proveedor (
    id_recoleccion INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    id_proveedor INT,
    id_empleado INT,
    id_centro INT,
    observaciones TEXT,
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor),
    FOREIGN KEY (id_empleado) REFERENCES empleados(id_empleado),
    FOREIGN KEY (id_centro) REFERENCES centros_acopio(id_centro)
);

-- 10. Detalle de materiales recolectados a proveedores
CREATE TABLE detalle_recoleccion_proveedor (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_recoleccion INT,
    id_material INT,
    cantidad_kg DECIMAL(10,2),
    FOREIGN KEY (id_recoleccion) REFERENCES recoleccion_proveedor(id_recoleccion),
    FOREIGN KEY (id_material) REFERENCES materiales(id_material)
);

-- USUARIOS 

CREATE TABLE usuario (
  id_usuario INT(11) NOT NULL,
  nombre_usuario VARCHAR(50) DEFAULT NULL,
  contrasena VARCHAR(100) DEFAULT NULL,
  rol VARCHAR(20) DEFAULT NULL,
  estado VARCHAR(10) DEFAULT NULL
);


-- INSERTAR DATOS DE LAS TABLAS 

-- TIPOS DE MATERIAL
INSERT INTO tipos_material (nombre_tipo) VALUES 
('Plástico'),
('Vidrio'),
('Papel');

-- MATERIALES
INSERT INTO materiales (nombre_material, descripcion, id_tipo_material) VALUES
('Botellas PET', 'Botellas plásticas transparentes', 1),
('Envases de vidrio', 'Botellas o frascos de vidrio', 2),
('Periódicos', 'Papel periódico usado', 3);

-- CLIENTES
INSERT INTO clientes (nombre_cliente, cedula_ruc, direccion, telefono, correo) VALUES
('EcoVecinos', '1720012345', 'Calle Verde 123', '0991234567', 'ecovecinos@mail.com'),
('Junta Barrial La Paz', '1720098765', 'Av. Amazonas N23-45', '0987654321', 'lapazjb@mail.com'),
('Escuela Ambiental', '0998765432', 'Calle Río Coca 104', '0967894321', 'ambiental@mail.com');

-- EMPLEADOS
INSERT INTO empleados (nombre_empleado, cargo, telefono, correo) VALUES
('Carlos Pérez', 'Recolector', '0991122334', 'cperez@reciclaje.com'),
('Luisa León', 'Clasificadora', '0988877665', 'lleon@reciclaje.com'),
('Diana Torres', 'Supervisor', '0977766554', 'dtorres@reciclaje.com');

-- CENTROS DE ACOPIO
INSERT INTO centros_acopio (nombre_centro, direccion, id_responsable) VALUES
('Centro Norte', 'Av. La Prensa y Brasil', 3),
('Centro Sur', 'Av. Maldonado y Quisquis', 1),
('Centro Valle', 'Ruta Viva y Tumbaco', 2);

-- RECOLECCIONES (CLIENTES)
INSERT INTO recolecciones (fecha, id_cliente, id_empleado, id_centro, observaciones) VALUES
('2025-08-01', 1, 1, 1, 'Entrega comunitaria mensual'),
('2025-08-03', 2, 2, 2, 'Recolección en evento barrial'),
('2025-08-05', 3, 1, 3, 'Campaña educativa');

-- DETALLE RECOLECCIÓN (CLIENTES)
INSERT INTO detalle_recoleccion (id_recoleccion, id_material, cantidad_kg) VALUES
(1, 1, 12.5),
(1, 3, 8.0),
(2, 2, 15.3),
(3, 1, 10.0),
(3, 3, 4.5);

-- PROVEEDORES INDUSTRIALES
INSERT INTO proveedores (nombre_proveedor, tipo_proveedor, direccion, telefono, correo) VALUES
('Fábrica Plásticos Quito', 'Industrial', 'Av. Simón Bolívar Km 9', '022345678', 'plastico@fpq.com'),
('Hospital Central', 'Institucional', 'Av. Mariana de Jesús 102', '023456789', 'residuos@hospital.ec'),
('Imprenta La Moderna', 'Industrial', 'Av. 6 de Diciembre 500', '024567890', 'contacto@moderna.ec');

-- RECOLECCIONES (PROVEEDORES)
INSERT INTO recoleccion_proveedor (fecha, id_proveedor, id_empleado, id_centro, observaciones) VALUES
('2025-08-02', 1, 1, 1, 'Entrega semanal de plásticos'),
('2025-08-04', 2, 2, 2, 'Vidrios médicos reciclables'),
('2025-08-05', 3, 3, 3, 'Papeles sobrantes de impresión');

-- DETALLE RECOLECCIÓN (PROVEEDORES)
INSERT INTO detalle_recoleccion_proveedor (id_recoleccion, id_material, cantidad_kg) VALUES
(1, 1, 50.0),
(2, 2, 40.5),
(3, 3, 33.3);


-- Insertar usuario administrador

INSERT INTO usuario (id_usuario, nombre_usuario, contrasena, rol, estado) VALUES
(1, 'admin', '$2y$10$QoRICDFmvF1y/JxkZ6VJbOWt6dHEbNFmiqEnmpaWHOqnU359aqfZW', 'Administrador', 'activo');


-- OTORGAR PERMISOS Y ROLES


-- Crear usuarios con contraseña
CREATE USER 'administrador'@'localhost' IDENTIFIED BY 'admin123';
CREATE USER 'develop'@'localhost' IDENTIFIED BY 'develop123';
CREATE USER 'supervisor'@'localhost' IDENTIFIED BY 'supervisor123';

-- Permisos para el Administrador (acceso total a toda la base de datos)
GRANT ALL PRIVILEGES ON empresa_maquinaria.* TO 'administrador'@'localhost';

-- Permisos para el Desarrollador
GRANT SELECT, INSERT, UPDATE ON empresa_maquinaria.clientes TO 'develop'@'localhost';
GRANT SELECT, INSERT, UPDATE ON empresa_maquinaria.maquinarias TO 'develop'@'localhost';
GRANT SELECT, INSERT, UPDATE ON empresa_maquinaria.ordenes_alquiler TO 'develop'@'localhost';
GRANT SELECT, INSERT, UPDATE ON empresa_maquinaria.detalle_alquiler TO 'develop'@'localhost';
GRANT SELECT, INSERT, UPDATE ON empresa_maquinaria.disponibilidad_maquinaria TO 'develop'@'localhost';
GRANT SELECT, INSERT, UPDATE ON empresa_maquinaria.tecnicos TO 'develop'@'localhost';
GRANT SELECT, INSERT, UPDATE ON empresa_maquinaria.mantenimiento TO 'develop'@'localhost';
GRANT SELECT, INSERT, UPDATE ON empresa_maquinaria.tecnico_maquinaria TO 'develop'@'localhost';

-- Permisos para el Supervisor (solo SELECT e INSERT, sin UPDATE)
GRANT SELECT, INSERT ON empresa_maquinaria.clientes TO 'supervisor'@'localhost';
GRANT SELECT, INSERT ON empresa_maquinaria.maquinarias TO 'supervisor'@'localhost';
GRANT SELECT, INSERT ON empresa_maquinaria.ordenes_alquiler TO 'supervisor'@'localhost';
GRANT SELECT, INSERT ON empresa_maquinaria.detalle_alquiler TO 'supervisor'@'localhost';
GRANT SELECT, INSERT ON empresa_maquinaria.disponibilidad_maquinaria TO 'supervisor'@'localhost';
GRANT SELECT, INSERT ON empresa_maquinaria.tecnicos TO 'supervisor'@'localhost';
GRANT SELECT, INSERT ON empresa_maquinaria.mantenimiento TO 'supervisor'@'localhost';
GRANT SELECT, INSERT ON empresa_maquinaria.tecnico_maquinaria TO 'supervisor'@'localhost';

-- Aplicar cambios de privilegios
FLUSH PRIVILEGES;


