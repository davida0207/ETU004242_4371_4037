<?php

namespace app\models;

use PDO;

class DonModel
{
	private PDO $db;

	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

	public function listWithFilters(array $filters): array
	{
		$where = ['1=1'];
		$params = [];

		if (!empty($filters['categorie'])) {
			$where[] = 'a.categorie = :categorie';
			$params[':categorie'] = (string)$filters['categorie'];
		}
		if (!empty($filters['article_id'])) {
			$where[] = 'd.article_id = :article_id';
			$params[':article_id'] = (int)$filters['article_id'];
		}
		if (!empty($filters['start_date'])) {
			$where[] = 'd.date_don >= :start_date';
			$params[':start_date'] = (string)$filters['start_date'];
		}
		if (!empty($filters['end_date'])) {
			$where[] = 'd.date_don <= :end_date';
			$params[':end_date'] = (string)$filters['end_date'];
		}

		$sql = '
			SELECT
				d.id,
				d.article_id,
				d.quantite,
				d.date_don,
				d.source,
				d.note,
				d.locked,
				a.categorie,
				a.libelle,
				a.unite,
				a.prix_unitaire,
				COALESCE(SUM(al.quantite), 0) AS attribue_quantite
			FROM bngrc_dons d
			JOIN bngrc_articles a ON a.id = d.article_id
			LEFT JOIN bngrc_allocations al ON al.don_id = d.id
			WHERE ' . implode(' AND ', $where) . '
			GROUP BY
				d.id, d.article_id, d.quantite, d.date_don, d.source, d.note, d.locked,
				a.categorie, a.libelle, a.unite, a.prix_unitaire
			ORDER BY d.date_don DESC, d.id DESC
		';

		$st = $this->db->prepare($sql);
		$st->execute($params);
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getById(int $id): ?array
	{
		$st = $this->db->prepare('
			SELECT
				d.id,
				d.article_id,
				d.quantite,
				d.date_don,
				d.source,
				d.note,
				d.locked,
				a.categorie,
				a.libelle,
				a.unite,
				a.prix_unitaire,
				COALESCE(SUM(al.quantite), 0) AS attribue_quantite
			FROM bngrc_dons d
			JOIN bngrc_articles a ON a.id = d.article_id
			LEFT JOIN bngrc_allocations al ON al.don_id = d.id
			WHERE d.id=?
			GROUP BY
				d.id, d.article_id, d.quantite, d.date_don, d.source, d.note, d.locked,
				a.categorie, a.libelle, a.unite, a.prix_unitaire
		');
		$st->execute([$id]);
		$row = $st->fetch(PDO::FETCH_ASSOC);
		return $row ?: null;
	}

	public function create(int $articleId, float $quantite, string $dateDon, ?string $source, ?string $note): int
	{
		$st = $this->db->prepare('INSERT INTO bngrc_dons(article_id, quantite, date_don, source, note) VALUES (?,?,?,?,?)');
		$st->execute([$articleId, $quantite, $dateDon, $source, $note]);
		return (int)$this->db->lastInsertId();
	}

	public function update(int $id, int $articleId, float $quantite, string $dateDon, ?string $source, ?string $note): void
	{
		$st = $this->db->prepare('UPDATE bngrc_dons SET article_id=?, quantite=?, date_don=?, source=?, note=? WHERE id=?');
		$st->execute([$articleId, $quantite, $dateDon, $source, $note, $id]);
	}

	public function hasAllocations(int $id): bool
	{
		$st = $this->db->prepare('SELECT 1 FROM bngrc_allocations WHERE don_id=? LIMIT 1');
		$st->execute([$id]);
		return (bool)$st->fetchColumn();
	}

	public function delete(int $id): void
	{
		$st = $this->db->prepare('DELETE FROM bngrc_dons WHERE id=?');
		$st->execute([$id]);
	}

	public function allocationsForDon(int $id): array
	{
		$st = $this->db->prepare('
			SELECT
				al.id,
				al.quantite,
				al.created_at,
				b.id AS besoin_id,
				b.date_besoin,
				v.nom AS ville,
				r.nom AS region,
				a.libelle AS article,
				a.unite,
				dr.id AS dispatch_run_id,
				dr.ran_at AS dispatch_ran_at,
				dr.note AS dispatch_note
			FROM bngrc_allocations al
			JOIN bngrc_besoins b ON b.id = al.besoin_id
			JOIN bngrc_villes v ON v.id = b.ville_id
			JOIN bngrc_regions r ON r.id = v.region_id
			JOIN bngrc_articles a ON a.id = b.article_id
			LEFT JOIN bngrc_dispatch_runs dr ON dr.id = al.dispatch_run_id
			WHERE al.don_id=?
			ORDER BY b.date_besoin ASC, b.id ASC, al.id ASC
		');
		$st->execute([$id]);
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}
}
