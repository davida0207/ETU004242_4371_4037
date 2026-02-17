<?php

namespace app\models;

use PDO;

class AllocationModel
{
	private PDO $db;

	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

	/** Crée une allocation */
	public function create(int $dispatchRunId, int $donId, int $besoinId, float $quantite): int
	{
		$st = $this->db->prepare('
			INSERT INTO bngrc_allocations(dispatch_run_id, don_id, besoin_id, quantite)
			VALUES (?, ?, ?, ?)
		');
		$st->execute([$dispatchRunId, $donId, $besoinId, $quantite]);
		return (int)$this->db->lastInsertId();
	}

	/**
	 * Dons non totalement attribués, triés par date_don ASC, id ASC
	 * Chaque ligne contient reste_don = quantite - COALESCE(SUM(alloc),0)
	 */
	public function unsatisfiedDons(): array
	{
		$sql = '
			SELECT d.id,
			       d.article_id,
			       d.quantite,
			       d.date_don,
			       d.source,
			       a.libelle,
			       a.unite,
			       a.prix_unitaire,
			       a.categorie,
			       COALESCE(SUM(al.quantite), 0)                     AS attribue,
			       (d.quantite - COALESCE(SUM(al.quantite), 0))      AS reste_don
			FROM bngrc_dons d
			JOIN bngrc_articles a ON a.id = d.article_id
			LEFT JOIN bngrc_allocations al ON al.don_id = d.id
			GROUP BY d.id, d.article_id, d.quantite, d.date_don, d.source,
			         a.libelle, a.unite, a.prix_unitaire, a.categorie
			HAVING reste_don > 0
			ORDER BY d.date_don ASC, d.id ASC
		';
		return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Besoins ouverts (non totalement couverts, non supprimés) pour un article donné
	 * Triés par date_besoin ASC, id ASC
	 */
	public function openBesoinsByArticle(int $articleId): array
	{
		$st = $this->db->prepare('
			SELECT b.id,
			       b.ville_id,
			       b.quantite,
			       b.date_besoin,
			       v.nom AS ville,
			       r.nom AS region,
			       COALESCE(SUM(al.quantite), 0)                     AS attribue,
			       (b.quantite - COALESCE(SUM(al.quantite), 0))      AS reste_besoin
			FROM bngrc_besoins b
			JOIN bngrc_villes  v ON v.id = b.ville_id
			JOIN bngrc_regions r ON r.id = v.region_id
			LEFT JOIN bngrc_allocations al ON al.besoin_id = b.id
			WHERE b.article_id = ? AND b.deleted_at IS NULL
			GROUP BY b.id, b.ville_id, b.quantite, b.date_besoin, v.nom, r.nom
			HAVING reste_besoin > 0
			ORDER BY b.date_besoin ASC, b.id ASC
		');
		$st->execute([$articleId]);
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	/** Stats globales rapides */
	public function globalStats(): array
	{
		$dons = $this->db->query('
			SELECT COUNT(*) AS nb,
			       COALESCE(SUM(d.quantite - COALESCE(al_sum.att, 0)), 0) AS reste_total
			FROM bngrc_dons d
			LEFT JOIN (
			    SELECT don_id, SUM(quantite) AS att FROM bngrc_allocations GROUP BY don_id
			) al_sum ON al_sum.don_id = d.id
		')->fetch(PDO::FETCH_ASSOC);

		$besoins = $this->db->query('
			SELECT COUNT(*) AS nb,
			       COALESCE(SUM(b.quantite - COALESCE(al_sum.att, 0)), 0) AS reste_total
			FROM bngrc_besoins b
			LEFT JOIN (
			    SELECT besoin_id, SUM(quantite) AS att FROM bngrc_allocations GROUP BY besoin_id
			) al_sum ON al_sum.besoin_id = b.id
			WHERE b.deleted_at IS NULL
		')->fetch(PDO::FETCH_ASSOC);

		$donsRestant = $this->db->query('
			SELECT COUNT(*) AS nb
			FROM bngrc_dons d
			LEFT JOIN (
			    SELECT don_id, SUM(quantite) AS att FROM bngrc_allocations GROUP BY don_id
			) al_sum ON al_sum.don_id = d.id
			WHERE (d.quantite - COALESCE(al_sum.att, 0)) > 0
		')->fetchColumn();

		$besoinsRestant = $this->db->query('
			SELECT COUNT(*) AS nb
			FROM bngrc_besoins b
			LEFT JOIN (
			    SELECT besoin_id, SUM(quantite) AS att FROM bngrc_allocations GROUP BY besoin_id
			) al_sum ON al_sum.besoin_id = b.id
			WHERE b.deleted_at IS NULL AND (b.quantite - COALESCE(al_sum.att, 0)) > 0
		')->fetchColumn();

		return [
			'dons_total'       => (int)($dons['nb'] ?? 0),
			'besoins_total'    => (int)($besoins['nb'] ?? 0),
			'dons_restant'     => (int)$donsRestant,
			'besoins_restant'  => (int)$besoinsRestant,
		];
	}
}
