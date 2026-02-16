<?php

namespace app\models;

use PDO;

class BesoinModel
{
	private PDO $db;

	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

	public function listWithFilters(array $filters): array
	{
		$where = ['b.deleted_at IS NULL'];
		$params = [];

		if (!empty($filters['region_id'])) {
			$where[] = 'v.region_id = :region_id';
			$params[':region_id'] = (int)$filters['region_id'];
		}
		if (!empty($filters['ville_id'])) {
			$where[] = 'b.ville_id = :ville_id';
			$params[':ville_id'] = (int)$filters['ville_id'];
		}
		if (!empty($filters['categorie'])) {
			$where[] = 'a.categorie = :categorie';
			$params[':categorie'] = (string)$filters['categorie'];
		}
		if (!empty($filters['article_id'])) {
			$where[] = 'b.article_id = :article_id';
			$params[':article_id'] = (int)$filters['article_id'];
		}
		if (!empty($filters['start_date'])) {
			$where[] = 'b.date_besoin >= :start_date';
			$params[':start_date'] = (string)$filters['start_date'];
		}
		if (!empty($filters['end_date'])) {
			$where[] = 'b.date_besoin <= :end_date';
			$params[':end_date'] = (string)$filters['end_date'];
		}

		$sql = '
			SELECT
				b.id,
				b.ville_id,
				b.article_id,
				b.quantite,
				b.date_besoin,
				b.note,
				r.nom AS region,
				v.nom AS ville,
				a.categorie,
				a.libelle,
				a.unite,
				a.prix_unitaire,
				COALESCE(SUM(al.quantite), 0) AS attribue_quantite
			FROM bngrc_besoins b
			JOIN bngrc_villes v ON v.id = b.ville_id
			JOIN bngrc_regions r ON r.id = v.region_id
			JOIN bngrc_articles a ON a.id = b.article_id
			LEFT JOIN bngrc_allocations al ON al.besoin_id = b.id
			WHERE ' . implode(' AND ', $where) . '
			GROUP BY
				b.id, b.ville_id, b.article_id, b.quantite, b.date_besoin, b.note,
				r.nom, v.nom, a.categorie, a.libelle, a.unite, a.prix_unitaire
			ORDER BY b.date_besoin DESC, b.id DESC
		';

		$st = $this->db->prepare($sql);
		$st->execute($params);
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getById(int $id): ?array
	{
		$st = $this->db->prepare('
			SELECT
				b.id,
				b.ville_id,
				b.article_id,
				b.quantite,
				b.date_besoin,
				b.note,
				b.deleted_at,
				r.nom AS region,
				v.nom AS ville,
				a.categorie,
				a.libelle,
				a.unite,
				a.prix_unitaire,
				COALESCE(SUM(al.quantite), 0) AS attribue_quantite
			FROM bngrc_besoins b
			JOIN bngrc_villes v ON v.id = b.ville_id
			JOIN bngrc_regions r ON r.id = v.region_id
			JOIN bngrc_articles a ON a.id = b.article_id
			LEFT JOIN bngrc_allocations al ON al.besoin_id = b.id
			WHERE b.id = ?
			GROUP BY
				b.id, b.ville_id, b.article_id, b.quantite, b.date_besoin, b.note, b.deleted_at,
				r.nom, v.nom, a.categorie, a.libelle, a.unite, a.prix_unitaire
		');
		$st->execute([$id]);
		$row = $st->fetch(PDO::FETCH_ASSOC);
		return $row ?: null;
	}

	public function create(int $villeId, int $articleId, float $quantite, string $dateBesoin, ?string $note): int
	{
		$st = $this->db->prepare('INSERT INTO bngrc_besoins(ville_id, article_id, quantite, date_besoin, note) VALUES (?,?,?,?,?)');
		$st->execute([$villeId, $articleId, $quantite, $dateBesoin, $note]);
		return (int)$this->db->lastInsertId();
	}

	public function update(int $id, int $villeId, int $articleId, float $quantite, string $dateBesoin, ?string $note): void
	{
		$st = $this->db->prepare('UPDATE bngrc_besoins SET ville_id=?, article_id=?, quantite=?, date_besoin=?, note=? WHERE id=?');
		$st->execute([$villeId, $articleId, $quantite, $dateBesoin, $note, $id]);
	}

	public function hasAllocations(int $id): bool
	{
		$st = $this->db->prepare('SELECT 1 FROM bngrc_allocations WHERE besoin_id=? LIMIT 1');
		$st->execute([$id]);
		return (bool)$st->fetchColumn();
	}

	public function softDelete(int $id): void
	{
		$st = $this->db->prepare('UPDATE bngrc_besoins SET deleted_at=NOW() WHERE id=?');
		$st->execute([$id]);
	}

	public function allocationsForBesoin(int $id): array
	{
		$st = $this->db->prepare('
			SELECT
				al.id,
				al.quantite,
				al.created_at,
				d.id AS don_id,
				d.date_don,
				d.source
			FROM bngrc_allocations al
			JOIN bngrc_dons d ON d.id = al.don_id
			WHERE al.besoin_id=?
			ORDER BY d.date_don ASC, d.id ASC, al.id ASC
		');
		$st->execute([$id]);
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}
}
