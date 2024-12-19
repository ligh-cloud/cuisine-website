CREATE DATABASE cuisine;

USE cuisine;


CREATE TABLE user (
    id_client INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    password VARCHAR(150) NOT NULL,

    email VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE reservation (
    reservation_id INT AUTO_INCREMENT PRIMARY KEY,
    date_reservation DATE NOT NULL,
    id_client INT NOT NULL,
    FOREIGN KEY (id_client) REFERENCES user(id_client) ,
    FOREIGN KEY (id_menu) REFERENCES menu(id_menu) ,
    nombre_place INT NOT NULL,
    create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('en attente', 'confirmer', 'annuler') DEFAULT 'en attente'
);


CREATE TABLE menu (
    id_menu INT AUTO_INCREMENT PRIMARY KEY,
    menu_name VARCHAR(100) NOT NULL,
    dish_name VARCHAR(200) NOT NULL,
    number_dish INT NOT NULL,
    id_client INT NOT NULL,
    FOREIGN KEY (id_client) REFERENCES user(id_client) 
);

CREATE TABLE dishes (
    id_dish INT AUTO_INCREMENT PRIMARY KEY,
    id_menu INT NOT NULL,
    dish_name VARCHAR(150) NOT NULL,
    ingrediant TEXT,
    image_url MEDIUMBLOB DEFAULT 'dish.jpg',
    FOREIGN KEY (id_menu) REFERENCES menu(id_menu) 
);


CREATE TABLE role (
    id_role INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('admin', 'user') NOT NULL,
    id_client INT NOT NULL,
    FOREIGN KEY (id_client) REFERENCES user(id_client) 
);
create TABLE menu_dish(
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_dish INT NOT NULL,
    id_menu INT NOT NULL,
    FOREIGN KEY (id_menu) REFERENCES menu(id_menu),
    FOREIGN KEY (id_dish) REFERENCES dishes(id_dish)

)
