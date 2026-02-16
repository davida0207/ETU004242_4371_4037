-- Validation queries

SELECT r.nom AS region,
       v.nom AS ville,
       SUM(b.quantite * a.prix_unitaire) AS valeur_besoins
FROM bngrc_besoins b
JOIN bngrc_villes v ON v.id = b.ville_id
JOIN bngrc_regions r ON r.id = v.region_id
JOIN bngrc_articles a ON a.id = b.article_id
WHERE b.deleted_at IS NULL
GROUP BY r.nom, v.nom
ORDER BY valeur_besoins DESC;

SELECT a.categorie,
       a.libelle,
       SUM(d.quantite) AS dons_total,
       (SUM(d.quantite) - COALESCE(SUM(al.quantite),0)) AS dons_restant
FROM bngrc_dons d
JOIN bngrc_articles a ON a.id = d.article_id
LEFT JOIN bngrc_allocations al ON al.don_id = d.id
GROUP BY a.categorie, a.libelle
ORDER BY a.categorie, a.libelle;

SELECT r.nom AS region,
       v.nom AS ville,
       a2.libelle AS article,
       SUM(al.quantite) AS quantite_attribuee
FROM bngrc_allocations al
JOIN bngrc_besoins b ON b.id = al.besoin_id
JOIN bngrc_villes v ON v.id = b.ville_id
JOIN bngrc_regions r ON r.id = v.region_id
JOIN bngrc_articles a2 ON a2.id = b.article_id
GROUP BY r.nom, v.nom, a2.libelle
ORDER BY r.nom, v.nom, a2.libelle;
