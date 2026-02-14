CREATE DATABASE IF NOT EXISTS entradas_online;
USE entradas_online;

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(50),
    dni INT,
    edad INT,
    nacimiento DATE,
    password VARCHAR(255)
);

INSERT INTO usuarios (email, dni, edad, nacimiento, password) VALUES 
('fedepolito@gmail.com', 40863247, 26, '1998-09-09', 'fede123'),
('juan@gmail.com', 123456789, 35, '1989-03-22', 'contraseña123');

CREATE TABLE conciertos (
    id_concierto INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE,
    lugar VARCHAR(100)
);

INSERT INTO conciertos (fecha, lugar) VALUES 
('2025-08-01', 'Buenos Aires'),
('2025-09-10', 'Córdoba');

CREATE TABLE entradas (
    id_entrada INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    id_concierto INT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_concierto) REFERENCES conciertos(id_concierto)
);

INSERT INTO entradas (id_usuario, id_concierto) VALUES 
(1, 1),
(2, 2);

ALTER TABLE usuarios
ADD telefono VARCHAR(20),
ADD fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
ADD activo BOOLEAN DEFAULT TRUE,
ADD preferencias JSON;
