-- ============================================================
-- Script : ajout de la table bngrc_settings
-- À exécuter sur une base BNGRC existante
-- ============================================================
USE BNGRC;

CREATE TABLE IF NOT EXISTS bngrc_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  setting_value VARCHAR(500) NOT NULL,
  label VARCHAR(200) NULL,
  updated_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insérer la valeur par défaut uniquement si elle n'existe pas encore
INSERT IGNORE INTO bngrc_settings (setting_key, setting_value, label) VALUES
('purchase_fee_percent', '10', 'Frais d''achat (%)');
