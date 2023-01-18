-- Ejemplo de script de implementación de BBDD (por ejemplo, 'bbdd.sql')
-- Creamos y empezamos a usar la BBDD

DROP DATABASE IF EXISTS bbdd_pizzeria;
CREATE DATABASE bbdd_pizzeria;
USE bbdd_pizzeria;

DROP TABLE IF EXISTS detalle_pedido;
DROP TABLE IF EXISTS detalle_pizza;
DROP TABLE IF EXISTS tallas;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS pizzas;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS ingredientes;


-- Implementación en SQL del modelo de base de datos

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),
    direccion VARCHAR(255),
    ciudad VARCHAR(100),
    telefono VARCHAR(15),
	email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
	rol VARCHAR(50)

);

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
	importe FLOAT,
	fecha DATE,
    modo_pago ENUM ('cash','credit card', 'transferencia') DEFAULT 'cash',
    modo_entrega ENUM ('local','recoger', 'domicilio') DEFAULT 'domicilio',
    comentarios VARCHAR(255),
    CONSTRAINT id_user_fk FOREIGN KEY (id_usuario) REFERENCES usuarios (id) ON DELETE CASCADE
      
);

-- categorías --

CREATE TABLE categorias (
	id INT AUTO_INCREMENT PRIMARY KEY,
    categoria VARCHAR(100),
    descripcion VARCHAR (255),
    imagen VARCHAR(255)
);

-- ingredientes adicionales --

CREATE TABLE ingredientes (
	id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion VARCHAR (255),
    imagen VARCHAR(255),
	precio FLOAT
);

CREATE TABLE pizzas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion VARCHAR (255),
    imagen VARCHAR(255),
    id_categoria INT NOT NULL,
    CONSTRAINT id_categoria_fk FOREIGN KEY (id_categoria) REFERENCES categorias (id) ON DELETE CASCADE

);

-- tamaños y precios items

CREATE TABLE tallas (
	id INT AUTO_INCREMENT PRIMARY KEY,
    talla VARCHAR(100),
    precio FLOAT,
    id_pizza INT NOT NULL,
    CONSTRAINT id_pizza_fk FOREIGN KEY (id_pizza) REFERENCES pizzas (id) ON DELETE CASCADE
);

-- items pedido --

CREATE TABLE detalle_pedido (
	id INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_pizza INT NOT NULL,
    id_talla INT NOT NULL,
    cantidad INT,
    CONSTRAINT id_talla_fk FOREIGN KEY (id_talla) REFERENCES tallas (id) ON DELETE CASCADE,
	CONSTRAINT id_pizza2_fk FOREIGN KEY (id_pizza) REFERENCES pizzas (id) ON DELETE CASCADE,
    CONSTRAINT id_pedido_fk FOREIGN KEY (id_pedido) REFERENCES pedidos (id) ON DELETE CASCADE
);

-- detalle item pedido --

CREATE TABLE detalle_pizza (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_detalle_pedido INT NOT NULL,
    id_ingrediente INT,
    cantidad INT DEFAULT 1,
    CONSTRAINT id_producto_fk FOREIGN KEY (id_detalle_pedido) REFERENCES detalle_pedido (id) ON DELETE CASCADE,
    CONSTRAINT id_ingrediente_fk FOREIGN KEY (id_ingrediente) REFERENCES ingredientes (id) ON DELETE CASCADE
);


-- Datos de ejemplo --

INSERT INTO usuarios (username, password, email, direccion, ciudad, telefono, rol) VALUES
('admin', '$2y$10$J0p3zn8xMQ/Hhb4lkuhr9e58/3Jl8YvqC.q6GlWJQ8/rF4qRcbop6','admin@gmail.com', 'calle ejemplo, 2', 'madrid','952336958', 'admin'),
('maria', '$2y$10$J0p3zn8xMQ/Hhb4lkuhr9e58/3Jl8YvqC.q6GlWJQ8/rF4qRcbop6','luis@gmail.com', 'calle ejemplo, 2', 'madrid', '952336958', 'user'),
('juan', '$2y$10$J0p3zn8xMQ/Hhb4lkuhr9e58/3Jl8YvqC.q6GlWJQ8/rF4qRcbop6','juan@gmail.com', 'calle ejemplo, 2', 'madrid', '952336958', 'user'),
('pepe', '$2y$10$J0p3zn8xMQ/Hhb4lkuhr9e58/3Jl8YvqC.q6GlWJQ8/rF4qRcbop6','pepe@gmail.com', 'calle ejemplo, 2', 'madrid', '952336958', 'user'),
('pedro', '$2y$10$J0p3zn8xMQ/Hhb4lkuhr9e58/3Jl8YvqC.q6GlWJQ8/rF4qRcbop6','pedro@gmail.com', 'calle ejemplo, 2', 'madrid', '952336958', 'user');

INSERT INTO ingredientes (nombre, descripcion, imagen, precio) VALUES
('atún', 'descripción','./img/pizza-margarita.jpg', 1.00),
('carne', 'descripción','./img/pizza-margarita.jpg', 2.00),
('jamón', 'descripción','./img/pizza-margarita.jpg', 1.50),
('queso', 'descripción','./img/pizza-margarita.jpg', 1.80),
('bacon', 'descripción','./img/pizza-margarita.jpg', 1.50);

INSERT INTO categorias (categoria, descripcion, imagen) VALUES
('crea tu pizza', ' Crea pizza a tu gusto. Escoge el tamaño y los ingredientes que prefieras','./img/a_tu_gusto.jpg'),
('especialidades', 'Elige entre todas nuestras especialidades','./img/especialidad.jpg'),
('clásicas', 'Los clásicos de siempre','./img/especialidad.jpg');


INSERT INTO pizzas (nombre, descripcion, imagen, id_categoria) VALUES
('pizza base', 'Crea tu propia pizza original. Sobre una base de salsa de tomate y queso 100% mozzarella, añade los ingredientes que tú quieras','./img/margarita.png',1),
('margarita','Salsa de tomate y queso 100% mozzarella','./img/margarita.png', 2),
('boloñesa','Salsa de tomate, extra de queso 100% mozzarella, carne de vacuno, bacon, pepperoni, york','./img/barbacoa.png', 2 ),
('carbonara','Crema fresca, queso 100% mozzarella, bacon, champiñón y cebolla','./img/carbonara.png', 2),
('cuatro quesos','Salsa de tomate, queso 100% mozzarella, cheddar, emmental, gorgonzola','./img/cuatro_quesos.png', 2),
('atún y bacon','Salsa de tomate, queso 100% mozzarella, atún y bacon','./img/barbacoa.png', 2),
('barbacoa','Salsa barbacoa, queso 100% mozzarella,carne de vacuno, cebolla, bacon, maíz','./img/barbacoa.png', 2);


INSERT INTO tallas (id_pizza, talla, precio) VALUES
(1, 'pequeña' , 10),
(1, 'mediana', 12),
(1, 'familiar', 14),

(2, 'pequeña' , 11),
(2, 'mediana', 13),
(2, 'familiar', 15),

(3, 'pequeña' , 13),
(3, 'mediana', 14),
(3, 'familiar', 16),

(4, 'pequeña' , 10),
(4, 'mediana', 13),
(4, 'familiar', 17),

(5, 'pequeña' , 9),
(5, 'mediana', 11),
(5, 'familiar', 13),

(6, 'pequeña' , 10),
(6, 'mediana', 12),
(6, 'familiar', 14),

(7, 'pequeña' , 10),
(7, 'mediana', 13),
(7, 'familiar', 14);


INSERT INTO pedidos (id, id_usuario, importe, fecha, modo_pago) VALUES 
('1', '2', 36.00,'2022-05-12', 'cash'),
('2', '2', 45.00,'2022-06-15', 'credit card'),
('3', '3', 13.00,'2022-07-18', 'cash'),
('4', '4', 13.00,'2022-09-21', 'credit card');

INSERT INTO detalle_pedido (id_pedido, id_pizza, cantidad, id_talla) VALUES
('1', '1', 1, 1),
('1', '2', 2, 5),
('2', '2', 3, 6),
('3', '3', 1, 7),
('4', '2', 1, 5);

INSERT INTO detalle_pizza (id_detalle_pedido, id_ingrediente, cantidad) VALUES
(1, 1, 1),
(1, 2, 1),
(1, 3, 1),
(1, 4, 1),
(2, 1, 1),
(2, 4, 1);



