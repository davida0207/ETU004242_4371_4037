<?php

namespace app\models;

use PDO;

class AchatModel
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

		if (!empty($filters['ville_id'])) {
			$where[] = 'a2.ville_id = :ville_id';
			$params[':ville_id'] = (int)$filters['ville_id'];
		}
		if (!empty($filters['start_date'])) {
			$where[] = 'a2.date_achat >= :start_date';
			$params[':start_date'] = (string)$filters['start_date'];
		}
		if (!empty($filters['end_date'])) {
			$where[] = 'a2.date_achat <= :end_date';
			$params[':end_date'] = (string)$filters['end_date'];
		}

		$sql = '
			SELECT
				a2.id,
				a2.besoin_id,
				a2.ville_id,
				a2.article_id,
				a2.quantite,
				a2.montant_base,
				a2.frais_percent,
				a2.montant_total,
				a2.date_achat,
				a2.note,
				v.nom AS ville,
				r.nom AS region,
				ar.libelle,
				ar.unite
			FROM bngrc_achats a2
			JOIN bngrc_villes v ON v.id = a2.ville_id
			JOIN bngrc_regions r ON r.id = v.region_id
			JOIN bngrc_articles ar ON ar.id = a2.article_id
			WHERE ' . implode(' AND ', $where) . '
			ORDER BY a2.date_achat DESC, a2.id DESC
		';

		$st = $this->db->prepare($sql);
		$st->execute($params);
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	public function totalSpent(): float
	{
		$st = $this->db->query('SELECT COALESCE(SUM(montant_total),0) FROM bngrc_achats');
		return (float)$st->fetchColumn();
	}

	public function insert(int $besoinId, int $villeId, int $articleId, float $quantite, float $montantBase, float $fraisPercent, float $montantTotal, string $dateAchat, ?string $note): int
	{
		$st = $this->db->prepare('INSERT INTO bngrc_achats(besoin_id, ville_id, article_id, quantite, montant_base, frais_percent, montant_total, date_achat, note) VALUES (?,?,?,?,?,?,?,?,?)');
		$st->execute([
			$besoinId > 0 ? $besoinId : null,
			$villeId,
			$articleId,
			$quantite,
			$montantBase,
			$fraisPercent,
			$montantTotal,
			$dateAchat,
			$note,
		]);
		return (int)$this->db->lastInsertId();
	}
}
