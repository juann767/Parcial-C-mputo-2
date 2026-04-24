-- =============================================
-- Sistema de Inscripción - Universidad Gerardo Barrios
-- Ejecutar este script en phpMyAdmin (XAMPP)
-- =============================================

CREATE DATABASE IF NOT EXISTS ugb_inscripcion CHARACTER SET utf8 COLLATE utf8_general_ci;
USE ugb_inscripcion;

-- Tabla de carreras
CREATE TABLE IF NOT EXISTS carreras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    facultad VARCHAR(100) NOT NULL,
    duracion_anios INT NOT NULL,
    descripcion TEXT NULL  -- campo que acepta valores nulos
);

-- Tabla de estudiantes
CREATE TABLE IF NOT EXISTS estudiantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    apellido VARCHAR(80) NOT NULL,
    carrera_id INT NOT NULL,
    turno ENUM('Matutino','Vespertino','Nocturno') NOT NULL,
    telefono VARCHAR(15) NULL,  -- campo que acepta valores nulos
    fecha_inscripcion DATE NOT NULL,
    FOREIGN KEY (carrera_id) REFERENCES carreras(id)
);

-- Tabla de usuarios (login)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- =============================================
-- Datos de carreras (5 registros)
-- =============================================
INSERT INTO carreras (nombre, facultad, duracion_anios, descripcion) VALUES
('Ingeniería en Sistemas', 'Ingeniería y Arquitectura', 5, 'Desarrollo de software y sistemas informáticos'),
('Administración de Empresas', 'Ciencias Económicas', 4, 'Gestión y administración empresarial'),
('Contaduría Pública', 'Ciencias Económicas', 4, NULL),
('Derecho', 'Ciencias Jurídicas', 5, 'Estudio de las leyes y el sistema jurídico salvadoreño'),
('Medicina', 'Ciencias de la Salud', 6, 'Formación médica integral con práctica clínica');

-- =============================================
-- Datos de estudiantes (6 registros)
-- =============================================
INSERT INTO estudiantes (nombre, apellido, carrera_id, turno, telefono, fecha_inscripcion) VALUES
('Carlos', 'Martínez', 1, 'Matutino', '78901234', '2025-01-15'),
('Ana', 'López', 2, 'Vespertino', '76543210', '2025-01-16'),
('José', 'González', 1, 'Nocturno', NULL, '2025-01-17'),
('María', 'Hernández', 3, 'Matutino', '70123456', '2025-01-18'),
('Luis', 'Pérez', 4, 'Vespertino', NULL, '2025-01-19'),
('Sofía', 'Ramírez', 5, 'Matutino', '79876543', '2025-01-20');

-- =============================================
-- NOTA: El usuario administrador se crea
-- ejecutando crear_admin.php UNA SOLA VEZ.
-- Credenciales: admin / admin123
-- =============================================
