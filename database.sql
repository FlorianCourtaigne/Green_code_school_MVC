DROP DATABASE IF EXISTS green_code_school;
CREATE DATABASE green_code_school;
USE green_code_school;

CREATE TABLE promo (
    id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    title VARCHAR(255) NOT NULL,
    date_start DATE,
    date_end DATE
);

CREATE TABLE user (
    id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    firstname VARCHAR(80) NOT NULL,
    lastname VARCHAR(80) NOT NULL,
    email VARCHAR(255) NOT NULL,
    pswd VARCHAR(32) NOT NULL,
    bio VARCHAR(255) NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_admin BOOLEAN DEFAULT 0,
    fridge_used BOOLEAN DEFAULT 0,
    promo_id INT NOT NULL,
    CONSTRAINT fk_user_promoid FOREIGN KEY (promo_id) REFERENCES promo(id)
);

CREATE TABLE planning (
    id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    week INT,
    date_start DATE,
    date_end DATE
);

CREATE TABLE planning_promo (
    planning_id INT NOT NULL,
    CONSTRAINT fk_planning_promo_planningid FOREIGN KEY (planning_id) REFERENCES planning(id),
    promo_id INT NOT NULL,
    CONSTRAINT fk_planning_promo_promoid FOREIGN KEY (promo_id) REFERENCES promo(id)
);

CREATE TABLE comment (
    id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INT NOT NULL,
    CONSTRAINT fk_comment_userid FOREIGN KEY (user_id) REFERENCES user(id)
);
