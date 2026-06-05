
CREATE DATABASE IF NOT EXISTS banque_spa;
USE banque_spa;

CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE,
    telephone VARCHAR(30),
    adresse TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE comptes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_compte VARCHAR(30) UNIQUE NOT NULL,
    client_id INT NOT NULL,
    solde DECIMAL(12,2) DEFAULT 0,
    type_compte ENUM('courant', 'epargne') DEFAULT 'courant',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compte_id INT NOT NULL,
    type ENUM('depot', 'retrait', 'virement') NOT NULL,
    montant DECIMAL(12,2) NOT NULL,
    description TEXT,
    date_transaction TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (compte_id) REFERENCES comptes(id) ON DELETE CASCADE
);


CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'agent') DEFAULT 'agent',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


INSERT INTO clients (nom, prenom, email, telephone, adresse)
VALUES 
('Rakoto', 'Jean', 'jean@gmail.com', '0341234567', 'Antananarivo'),
('Rabe', 'Marie', 'marie@gmail.com', '0329876543', 'Toamasina');

INSERT INTO comptes (numero_compte, client_id, solde, type_compte)
VALUES 
('C0001', 1, 500000, 'courant'),
('C0002', 2, 1200000, 'epargne');

INSERT INTO utilisateurs (username, password, role)
VALUES 
('admin', MD5('admin123'), 'admin');