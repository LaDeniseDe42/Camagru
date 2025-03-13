CREATE DATABASE IF NOT EXISTS mydatabase;
USE mydatabase;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    confirmation_token VARCHAR(255) NOT NULL,
    is_confirmed TINYINT(1) DEFAULT 0,
    house ENUM('Gryffondor', 'Poufsouffle', 'Serdaigle', 'Serpentard', 'Moldu', 'Crakmol') DEFAULT 'Moldu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,  -- Nom du fichier sur le serveur
    filepath VARCHAR(255) NOT NULL,  -- Chemin d'accès au fichier
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);