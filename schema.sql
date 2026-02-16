CREATE DATABASE IF NOT EXISTS metis CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE metis;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  prenom VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  telephone VARCHAR(20) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
                                                                              
CREATE TABLE IF NOT EXISTS chat (
  id INT AUTO_INCREMENT PRIMARY KEY,
  message VARCHAR(500) NOT NULL,
  sender_id INT NOT NULL,
  receiver_id INT NOT NULL,
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status ENUM('sent', 'delivered', 'read') DEFAULT 'sent'
);
