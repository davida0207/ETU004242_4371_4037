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
	public function create(?string $note = null, string $methode = 'fifo'): int
	{
		$st = $this->db->prepare('INSERT INTO bngrc_dispatch_runs(note, methode) VALUES (?, ?)');
		$st->execute([$note, $methode]);
		return (int)$this->db->lastInsertId();
	}

	/** Liste tous les runs (du plus récent au plus ancien) */
	public function listAll(): array
	{
		$sql = '
			SELECT dr.id,
			       dr.ran_at,
			       dr.note,
			       dr.methode,
			       COUNT(al.id)                                  AS nb_allocations,
			       COALESCE(SUM(al.quantite), 0)                AS total_quantite,
			       COUNT(DISTINCT al.don_id)                     AS nb_dons,
			       COUNT(DISTINCT al.besoin_id)                  AS nb_besoins
			FROM bngrc_dispatch_runs dr
			LEFT JOIN bngrc_allocations al ON al.dispatch_run_id = dr.id
			GROUP BY dr.id, dr.ran_at, dr.note, dr.methode
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
			       dr.methode,
			       COUNT(al.id)                  AS nb_allocations,
			       COALESCE(SUM(al.quantite), 0) AS total_quantite,
			       COUNT(DISTINCT al.don_id)      AS nb_dons,
			       COUNT(DISTINCT al.besoin_id)   AS nb_besoins
			FROM bngrc_dispatch_runs dr
			LEFT JOIN bngrc_allocations al ON al.dispatch_run_id = dr.id
			WHERE dr.id = ?
			GROUP BY dr.id, dr.ran_at, dr.note, dr.methode
		');
		$st->execute([$id]);
		$row = $st->fetch(PDO::FETCH_ASSOC);
		return $row ?: null;
	}

	/** Détail des allocations d'un run */
	public function allocationsForRun(int $runId, string $methode = 'fifo'): array
	{
		$orderClause = match ($methode) {
			'smallest'     => 'ORDER BY a.libelle ASC, b.quantite ASC, b.id ASC, d.date_don ASC, d.id ASC',
			'proportional' => 'ORDER BY a.libelle ASC, b.date_besoin ASC, b.id ASC, d.date_don ASC, d.id ASC',
			default        => 'ORDER BY a.libelle ASC, b.date_besoin ASC, b.id ASC, d.date_don ASC, d.id ASC',  // fifo
		};

		$st = $this->db->prepare("
			SELECT al.id,
			       al.quantite,
			       al.created_at,
			       d.id           AS don_id,
			       d.quantite     AS don_quantite,
			       d.date_don,
			       d.source       AS don_source,
			       d.note         AS don_note,
			       b.id           AS besoin_id,
			       b.quantite     AS besoin_quantite,
			       b.date_besoin,
			       b.note         AS besoin_note,
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
			{$orderClause}
		");
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
