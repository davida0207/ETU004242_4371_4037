-- Seed data for BNGRC schema

INSERT INTO bngrc_regions(nom) VALUES
  ('Analamanga'),
  ('Atsinanana'),
  ('Vakinankaratra')
ON DUPLICATE KEY UPDATE nom=VALUES(nom);

-- Create cities (use subqueries to map region names)
INSERT INTO bngrc_villes(region_id, nom)
SELECT r.id, v.nom
FROM bngrc_regions r
JOIN (
  SELECT 'Analamanga' AS region_nom, 'Antananarivo' AS nom UNION ALL
  SELECT 'Analamanga', 'Ambohidratrimo' UNION ALL
  SELECT 'Atsinanana', 'Toamasina' UNION ALL
  SELECT 'Atsinanana', 'Brickaville' UNION ALL
  SELECT 'Vakinankaratra', 'Antsirabe' UNION ALL
  SELECT 'Vakinankaratra', 'Betafo'
) v ON v.region_nom = r.nom
ON DUPLICATE KEY UPDATE nom=VALUES(nom);

INSERT INTO bngrc_articles(categorie, libelle, unite, prix_unitaire, actif) VALUES
  ('nature', 'Riz', 'kg', 3500, 1),
  ('nature', 'Huile', 'L', 9000, 1),
  ('materiau', 'Tôle', 'piece', 25000, 1),
  ('materiau', 'Clou', 'kg', 12000, 1),
  ('argent', 'Don en argent', 'Ar', 1, 1)
ON DUPLICATE KEY UPDATE actif=VALUES(actif);

-- Sample needs
INSERT INTO bngrc_besoins(ville_id, article_id, quantite, date_besoin, note)
SELECT vi.id, a.id, b.quantite, b.date_besoin, b.note
FROM (
  SELECT 'Antananarivo' AS ville, 'Riz' AS article, 500 AS quantite, '2026-02-10' AS date_besoin, 'Besoin urgent' AS note UNION ALL
  SELECT 'Toamasina', 'Riz', 700, '2026-02-11', 'Inondations' UNION ALL
  SELECT 'Antsirabe', 'Tôle', 120, '2026-02-09', 'Réparation toitures' UNION ALL
  SELECT 'Brickaville', 'Huile', 80, '2026-02-12', NULL
) b
JOIN bngrc_villes vi ON vi.nom = b.ville
JOIN bngrc_articles a ON a.libelle = b.article
WHERE NOT EXISTS (
  SELECT 1 FROM bngrc_besoins bx WHERE bx.ville_id=vi.id AND bx.article_id=a.id AND bx.date_besoin=b.date_besoin
);

-- Sample donations
INSERT INTO bngrc_dons(article_id, quantite, date_don, source, note)
SELECT a.id, d.quantite, d.date_don, d.source, d.note
FROM (
  SELECT 'Riz' AS article, 600 AS quantite, '2026-02-08' AS date_don, 'Collecte A' AS source, NULL AS note UNION ALL
  SELECT 'Tôle', 50, '2026-02-10', 'Entreprise B', NULL UNION ALL
  SELECT 'Don en argent', 2500000, '2026-02-11', 'Donateur C', 'Affectation libre'
) d
JOIN bngrc_articles a ON a.libelle = d.article
WHERE NOT EXISTS (
  SELECT 1 FROM bngrc_dons dx WHERE dx.article_id=a.id AND dx.date_don=d.date_don AND dx.quantite=d.quantite
);
