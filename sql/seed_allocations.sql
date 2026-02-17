-- =============================================================
-- Seed : bngrc_dispatch_runs + bngrc_allocations
-- À exécuter APRÈS base.sql et seed.sql
-- Date : 2026-02-16
-- =============================================================

-- 1) Créer une session de distribution
INSERT INTO bngrc_dispatch_runs (id, ran_at, note) VALUES
    (1, '2026-02-14 10:00:00', 'Distribution initiale — cyclone Gamane');

-- 2) Allocations : répartir les dons vers les besoins
--    don_id 1 (Riz,  600 kg) → besoin_id 1 (Antananarivo, 500 kg Riz)  : 350 kg
--    don_id 1 (Riz,  600 kg) → besoin_id 2 (Toamasina,    700 kg Riz)  : 250 kg
--    don_id 5 (Huile, 50 L)  → besoin_id 3 (Brickaville,   80 L Huile) :  40 L
--    don_id 2 (Tôle,  50 pc) → besoin_id 4 (Antsirabe,    120 pc Tôle) :  50 pc
INSERT INTO bngrc_allocations (dispatch_run_id, don_id, besoin_id, quantite, created_at) VALUES
    (1, 1, 1, 350.00, '2026-02-14 10:00:00'),
    (1, 1, 2, 250.00, '2026-02-14 10:00:00'),
    (1, 5, 3,  40.00, '2026-02-14 10:00:00'),
    (1, 2, 4,  50.00, '2026-02-14 10:00:00');
