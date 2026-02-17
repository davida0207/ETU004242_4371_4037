-- Base BNGRC schema (FK SIMPLE VERSION)
-- Tables are prefixed with bngrc_ to avoid conflicts.
-- This version keeps FOREIGN KEY constraints (data consistency)
-- but avoids explicit KEY (indexes) and UNIQUE keys for simplicity.
-- Note: InnoDB may create required indexes implicitly for foreign keys.
create database BNGRC;
use BNGRC;
CREATE TABLE IF NOT EXISTS bngrc_regions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(150) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bngrc_villes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  region_id INT NOT NULL,
  nom VARCHAR(150) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (region_id) REFERENCES bngrc_regions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bngrc_articles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  categorie ENUM('nature','materiau','argent') NOT NULL,
  libelle VARCHAR(150) NOT NULL,
  unite VARCHAR(50) NOT NULL,
  prix_unitaire DECIMAL(15,2) NOT NULL DEFAULT 0,
  actif TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bngrc_besoins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ville_id INT NOT NULL,
  article_id INT NOT NULL,
  quantite DECIMAL(15,2) NOT NULL,
  date_besoin DATE NOT NULL,
  note VARCHAR(500) NULL,
  deleted_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ville_id) REFERENCES bngrc_villes(id),
  FOREIGN KEY (article_id) REFERENCES bngrc_articles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bngrc_dons (
  id INT AUTO_INCREMENT PRIMARY KEY,
  article_id INT NOT NULL,
  quantite DECIMAL(15,2) NOT NULL,
  date_don DATE NOT NULL,
  source VARCHAR(200) NULL,
  note VARCHAR(500) NULL,
  locked TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (article_id) REFERENCES bngrc_articles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bngrc_dispatch_runs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ran_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  note VARCHAR(300) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bngrc_allocations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  dispatch_run_id INT NOT NULL,
  don_id INT NOT NULL,
  besoin_id INT NOT NULL,
  quantite DECIMAL(15,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (dispatch_run_id) REFERENCES bngrc_dispatch_runs(id),
  FOREIGN KEY (don_id) REFERENCES bngrc_dons(id),
  FOREIGN KEY (besoin_id) REFERENCES bngrc_besoins(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bngrc_achats (
  id INT AUTO_INCREMENT PRIMARY KEY,
  besoin_id INT NULL,
  ville_id INT NOT NULL,
  article_id INT NOT NULL,
  quantite DECIMAL(15,2) NOT NULL,
  montant_base DECIMAL(15,2) NOT NULL,
  frais_percent DECIMAL(5,2) NOT NULL,
  montant_total DECIMAL(15,2) NOT NULL,
  date_achat DATE NOT NULL,
  note VARCHAR(500) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (besoin_id) REFERENCES bngrc_besoins(id),
  FOREIGN KEY (ville_id) REFERENCES bngrc_villes(id),
  FOREIGN KEY (article_id) REFERENCES bngrc_articles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
