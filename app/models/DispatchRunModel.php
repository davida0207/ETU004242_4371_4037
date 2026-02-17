<?php

namespace app\models;

use PDO;

class DispatchRunModel
{
	private PDO $db;

	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

	/** Crée un nouveau dispatch run et renvoie son ID */
	public function create(?string $note = null): int
	{
		$st = $this->db->prepare('INSERT INTO bngrc_dispatch_runs(note) VALUES (?)');
		$st->execute([$note]);
		return (int)$this->db->lastInsertId();
	}

	/** Liste tous les runs (du plus récent au plus ancien) */
	public function listAll(): array
	{
		$sql = '
			SELECT dr.id,
			       dr.ran_at,
			       dr.note,
			       COUNT(al.id)                                  AS nb_allocations,
			       COALESCE(SUM(al.quantite), 0)                AS total_quantite,
			       COUNT(DISTINCT al.don_id)                     AS nb_dons,
			       COUNT(DISTINCT al.besoin_id)                  AS nb_besoins
			FROM bngrc_dispatch_runs dr
			LEFT JOIN bngrc_allocations al ON al.dispatch_run_id = dr.id
			GROUP BY dr.id, dr.ran_at, dr.note
			ORDER BY dr.ran_at DESC, dr.id DESC
		';
		return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	}

	/** Récupère un run par ID */
	public function getById(int $id): ?array
	{
		$st = $this->db->prepare('
			SELECT dr.id,
			       dr.ran_at,
			       dr.note,
			       COUNT(al.id)                  AS nb_allocations,
			       COALESCE(SUM(al.quantite), 0) AS total_quantite,
			       COUNT(DISTINCT al.don_id)      AS nb_dons,
			       COUNT(DISTINCT al.besoin_id)   AS nb_besoins
			FROM bngrc_dispatch_runs dr
			LEFT JOIN bngrc_allocations al ON al.dispatch_run_id = dr.id
			WHERE dr.id = ?
			GROUP BY dr.id, dr.ran_at, dr.note
		');
		$st->execute([$id]);
		$row = $st->fetch(PDO::FETCH_ASSOC);
		return $row ?: null;
	}

	/** Détail des allocations d'un run */
	public function allocationsForRun(int $runId): array
	{
		$st = $this->db->prepare('
			SELECT al.id,
			       al.quantite,
			       al.created_at,
			       d.id           AS don_id,
			       d.date_don,
			       d.source       AS don_source,
			       b.id           AS besoin_id,
			       b.date_besoin,
			       v.nom          AS ville,
			       r.nom          AS region,
			       a.libelle      AS article,
			       a.categorie,
			       a.unite,
			       a.prix_unitaire
			FROM bngrc_allocations al
			JOIN bngrc_dons    d  ON d.id  = al.don_id
			JOIN bngrc_besoins b  ON b.id  = al.besoin_id
			JOIN bngrc_villes  v  ON v.id  = b.ville_id
			JOIN bngrc_regions r  ON r.id  = v.region_id
			JOIN bngrc_articles a ON a.id  = b.article_id
			WHERE al.dispatch_run_id = ?
			ORDER BY a.libelle ASC, v.nom ASC, al.id ASC
		');
		$st->execute([$runId]);
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	/** Supprime un run et toutes ses allocations (cascade) */
	public function deleteRunAllocations(int $runId): int
	{
		$st = $this->db->prepare('DELETE FROM bngrc_allocations WHERE dispatch_run_id = ?');
		$st->execute([$runId]);
		$count = $st->rowCount();
		$this->db->prepare('DELETE FROM bngrc_dispatch_runs WHERE id = ?')->execute([$runId]);
		return $count;
	}

	/** Supprime TOUTES les allocations et TOUS les runs (reset complet) */
	public function resetAll(): int
	{
		$count = (int)$this->db->query('SELECT COUNT(*) FROM bngrc_allocations')->fetchColumn();
		$this->db->exec('DELETE FROM bngrc_allocations');
		$this->db->exec('DELETE FROM bngrc_dispatch_runs');
		return $count;
	}
}
