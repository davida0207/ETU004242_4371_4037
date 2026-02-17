-- =============================================================
-- migration_seed_dispatch_demo.sql
-- Nettoyage complet + nouvelles données de test pour démontrer
-- les 3 méthodes de dispatch : FIFO, Smallest, Proportionnel
-- Exécuté le 2026-02-17
-- =============================================================

-- 1) Nettoyage complet (ordre FK)
DELETE FROM bngrc_allocations;
DELETE FROM bngrc_dispatch_runs;
DELETE FROM bngrc_dons;
DELETE FROM bngrc_besoins;

-- Reset auto-increment
ALTER TABLE bngrc_allocations    AUTO_INCREMENT = 1;
ALTER TABLE bngrc_dispatch_runs  AUTO_INCREMENT = 1;
ALTER TABLE bngrc_dons           AUTO_INCREMENT = 1;
ALTER TABLE bngrc_besoins        AUTO_INCREMENT = 1;

-- =============================================================
-- 2) Besoins — variés en taille ET en date pour voir la diff
--    entre FIFO (date), Smallest (quantité), Proportionnel
-- =============================================================

-- Riz : 5 besoins, total = 2 000 kg
INSERT INTO bngrc_besoins(ville_id, article_id, quantite, date_besoin, note)
SELECT vi.id, a.id, b.qty, b.dt, b.note
FROM (
  SELECT 'Antananarivo'  AS ville, 'Riz' AS article, 800  AS qty, '2026-02-05' AS dt, 'Cyclone — familles déplacées'     AS note UNION ALL
  SELECT 'Toamasina',     'Riz',                      200,        '2026-02-08',       'Petit village isolé'              UNION ALL
  SELECT 'Antsirabe',     'Riz',                      500,        '2026-02-06',       'Centre d''hébergement'            UNION ALL
  SELECT 'Brickaville',   'Riz',                      150,        '2026-02-10',       'École sinistrée'                  UNION ALL
  SELECT 'Ambohidratrimo','Riz',                      350,        '2026-02-07',       'Distribution communale'
) b
JOIN bngrc_villes vi ON vi.nom = b.ville
JOIN bngrc_articles a ON a.libelle = b.article;

-- Tôle : 3 besoins, total = 300 pièces
INSERT INTO bngrc_besoins(ville_id, article_id, quantite, date_besoin, note)
SELECT vi.id, a.id, b.qty, b.dt, b.note
FROM (
  SELECT 'Antsirabe'     AS ville, 'Tôle' AS article, 120 AS qty, '2026-02-04' AS dt, 'Toitures détruites'        AS note UNION ALL
  SELECT 'Betafo',        'Tôle',                       30,        '2026-02-09',       'Petit atelier'             UNION ALL
  SELECT 'Toamasina',     'Tôle',                      150,        '2026-02-06',       'Marché communal effondré'
) b
JOIN bngrc_villes vi ON vi.nom = b.ville
JOIN bngrc_articles a ON a.libelle = b.article;

-- Huile : 3 besoins, total = 260 L
INSERT INTO bngrc_besoins(ville_id, article_id, quantite, date_besoin, note)
SELECT vi.id, a.id, b.qty, b.dt, b.note
FROM (
  SELECT 'Brickaville'   AS ville, 'Huile' AS article, 100 AS qty, '2026-02-07' AS dt, 'Cuisine collective'      AS note UNION ALL
  SELECT 'Antananarivo',   'Huile',                       60,        '2026-02-11',       'Cantine scolaire'       UNION ALL
  SELECT 'Ambohidratrimo', 'Huile',                      100,        '2026-02-08',       'Réserve communale'
) b
JOIN bngrc_villes vi ON vi.nom = b.ville
JOIN bngrc_articles a ON a.libelle = b.article;

-- Don en argent : 2 besoins, total = 4 000 000 Ar
INSERT INTO bngrc_besoins(ville_id, article_id, quantite, date_besoin, note)
SELECT vi.id, a.id, b.qty, b.dt, b.note
FROM (
  SELECT 'Antananarivo' AS ville, 'Don en argent' AS article, 1500000 AS qty, '2026-02-06' AS dt, 'Fonds d''urgence mairie' AS note UNION ALL
  SELECT 'Toamasina',    'Don en argent',                      2500000,        '2026-02-03',       'Reconstruction école'
) b
JOIN bngrc_villes vi ON vi.nom = b.ville
JOIN bngrc_articles a ON a.libelle = b.article;

-- =============================================================
-- 3) Dons — quantités inférieures aux besoins totaux pour
--    forcer un partage partiel (plus intéressant pour la démo)
-- =============================================================

-- Riz : 2 dons = 900 kg (besoins = 2 000 → partiel)
INSERT INTO bngrc_dons(article_id, quantite, date_don, source, note)
SELECT a.id, d.qty, d.dt, d.src, d.note
FROM (
  SELECT 'Riz' AS article, 500 AS qty, '2026-02-03' AS dt, 'Collecte nationale'    AS src, NULL                    AS note UNION ALL
  SELECT 'Riz',             400,        '2026-02-07',       'ONG Aide & Solidarité', 'Lot arrivé par camion'
) d
JOIN bngrc_articles a ON a.libelle = d.article;

-- Tôle : 1 don = 100 pièces (besoins = 300 → partiel)
INSERT INTO bngrc_dons(article_id, quantite, date_don, source, note)
SELECT a.id, d.qty, d.dt, d.src, d.note
FROM (
  SELECT 'Tôle' AS article, 100 AS qty, '2026-02-05' AS dt, 'Entreprise Cimenterie' AS src, 'Don matériaux' AS note
) d
JOIN bngrc_articles a ON a.libelle = d.article;

-- Huile : 1 don = 150 L (besoins = 260 → partiel)
INSERT INTO bngrc_dons(article_id, quantite, date_don, source, note)
SELECT a.id, d.qty, d.dt, d.src, d.note
FROM (
  SELECT 'Huile' AS article, 150 AS qty, '2026-02-06' AS dt, 'Supermarché Leader' AS src, NULL AS note
) d
JOIN bngrc_articles a ON a.libelle = d.article;

-- Argent : 1 don = 3 000 000 Ar (besoins = 4 000 000 → partiel)
INSERT INTO bngrc_dons(article_id, quantite, date_don, source, note)
SELECT a.id, d.qty, d.dt, d.src, d.note
FROM (
  SELECT 'Don en argent' AS article, 3000000 AS qty, '2026-02-04' AS dt, 'Banque de Madagascar' AS src, 'Virement solidarité' AS note
) d
JOIN bngrc_articles a ON a.libelle = d.article;

-- =============================================================
-- Résumé des données insérées :
--   Besoins : 13 lignes (5 Riz + 3 Tôle + 3 Huile + 2 Argent)
--   Dons    :  5 lignes (2 Riz + 1 Tôle + 1 Huile + 1 Argent)
--
--   Article        Besoins total   Dons total   Déficit
--   Riz            2 000 kg        900 kg       1 100 kg
--   Tôle           300 pcs         100 pcs      200 pcs
--   Huile          260 L           150 L        110 L
--   Argent         4 000 000 Ar    3 000 000 Ar 1 000 000 Ar
-- =============================================================