DROP DATABASE IF EXISTS ohmypet;

CREATE DATABASE ohmypet;

USE ohmypet;

DROP TABLE IF EXISTS categories;

CREATE TABLE categories (
  id int auto_increment NOT NULL,
  name varchar(50) NOT NULL,
  slug varchar(50) NOT NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS pets;

CREATE TABLE pets (
  id int auto_increment NOT NULL,
  name varchar(50) NOT NULL,
  slug varchar(50) NOT NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS users; 

CREATE TABLE users (
  id int auto_increment NOT NULL,
  name varchar(20) NOT NULL,
  last_name varchar(20) NOT NULL,
  email varchar(50) NOT NULL unique,
  password varchar(255) NOT NULL,
  reg_date date NOT NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS products;

CREATE TABLE products (
  id int auto_increment NOT NULL,
  categories varchar(250) NOT NULL,
  pet_id int NOT NULL,
  title varchar(250) NOT NULL,
  slug varchar(250) NOT NULL,
  price int(250) NOT NULL,
  thumb varchar(250) NOT NULL,
  post_date date NOT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY(pet_id) REFERENCES pets(id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS orders;

CREATE TABLE orders (
  id int auto_increment NOT NULL,
  products varchar(250) NOT NULL,
  user_id int NOT NULL,
  total int(250)NOT NULL,
  post_date date NOT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY(user_id) REFERENCES users(id)
) ENGINE=InnoDB;

INSERT INTO users (name, last_name, email, password, reg_date) VALUES
( 'Andrés', 'Ospina', 'admin@ohmypet.com', '$2y$10$i.ivTZnhG4vywALVhgFx4u.pPBYbuw5S2yJJ5AGGbnpDtwTF.9qwu', curdate() );

INSERT INTO pets ( name, slug ) VALUES 
( 'Perros', 'perros' ), 
( 'Gatos', 'gatos' );

INSERT INTO categories ( name, slug ) VALUES
( 'Aseo', 'aseo' ),
( 'Alimento', 'alimento' ),
( 'Deporte', 'deporte' ),
( 'Salud', 'salud' );