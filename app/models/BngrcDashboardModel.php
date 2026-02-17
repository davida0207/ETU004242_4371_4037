<?php

namespace app\models;

use PDO;
use RuntimeException;

class BngrcDashboardModel
{
	private PDO $db;

	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

	/**
	 * Résumé par ville : besoins totaux, attribué, quantités
	 */
	public function citySummary(): array
	{
		$st = $this->db->query('
			SELECT r.nom AS region,
				   v.id AS ville_id,
				   v.nom AS ville,
				   COALESCE(SUM(b.quantite * a.prix_unitaire), 0) AS besoins_valeur,
				   COALESCE(SUM(b.quantite), 0) AS besoins_quantite,
				   COALESCE(SUM(al.quantite * a.prix_unitaire), 0) AS attribue_valeur,
				   COALESCE(SUM(al.quantite), 0) AS attribue_quantite
			FROM bngrc_villes v
			JOIN bngrc_regions r ON r.id = v.region_id
			LEFT JOIN bngrc_besoins b ON b.ville_id = v.id AND b.deleted_at IS NULL
			LEFT JOIN bngrc_articles a ON a.id = b.article_id
			LEFT JOIN bngrc_allocations al ON al.besoin_id = b.id
			GROUP BY r.nom, v.id, v.nom
			ORDER BY r.nom ASC, v.nom ASC
		');
		if ($st === false) {
			$err = $this->db->errorInfo();
			throw new RuntimeException('Query failed: ' . (string)($err[2] ?? 'unknown error'));
		}
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Statistiques globales des dons :
	 *  - dons_total_valeur   : somme(quantite * prix_unitaire) de tous les dons
	 *  - dons_total_quantite : nombre de lignes de dons
	 *  - dons_attribue_valeur: somme(alloc.quantite * prix_unitaire) — ce qui a été distribué
	 */
	public function donStats(): array
	{
		$st = $this->db->query('
			SELECT COALESCE(SUM(d.quantite * a.prix_unitaire), 0) AS dons_total_valeur,
				   COUNT(d.id) AS dons_total_count,
				   COALESCE(SUM(d.quantite), 0) AS dons_total_quantite
			FROM bngrc_dons d
			JOIN bngrc_articles a ON a.id = d.article_id
		');
		$row = $st->fetch(PDO::FETCH_ASSOC);

		$st2 = $this->db->query('
			SELECT COALESCE(SUM(al.quantite * a.prix_unitaire), 0) AS dons_attribue_valeur
			FROM bngrc_allocations al
			JOIN bngrc_besoins b ON b.id = al.besoin_id
			JOIN bngrc_articles a ON a.id = b.article_id
		');
		$row2 = $st2->fetch(PDO::FETCH_ASSOC);

		return [
			'dons_total_valeur'    => (float)($row['dons_total_valeur'] ?? 0),
			'dons_total_count'     => (int)($row['dons_total_count'] ?? 0),
			'dons_total_quantite'  => (float)($row['dons_total_quantite'] ?? 0),
			'dons_attribue_valeur' => (float)($row2['dons_attribue_valeur'] ?? 0),
		];
	}
}
