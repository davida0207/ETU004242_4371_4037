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

	public function citySummary(): array
	{
		// Value-based summary (quantite * prix_unitaire)
		$st = $this->db->query('
			SELECT r.nom AS region,
				   v.id AS ville_id,
				   v.nom AS ville,
				   COALESCE(SUM(b.quantite * a.prix_unitaire),0) AS besoins_valeur,
				   COALESCE(SUM(al.quantite * a.prix_unitaire),0) AS attribue_valeur
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
}
