<?php

namespace app\models;

use PDO;

class VilleModel
{
	private PDO $db;

	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

	public function listAll(?int $regionId = null): array
	{
		if ($regionId) {
			$st = $this->db->prepare('
				SELECT v.id, v.nom, v.region_id, r.nom AS region_nom
				FROM bngrc_villes v
				JOIN bngrc_regions r ON r.id = v.region_id
				WHERE v.region_id=?
				ORDER BY v.nom ASC
			');
			$st->execute([$regionId]);
			return $st->fetchAll(PDO::FETCH_ASSOC);
		}

		$st = $this->db->query('
			SELECT v.id, v.nom, v.region_id, r.nom AS region_nom
			FROM bngrc_villes v
			JOIN bngrc_regions r ON r.id = v.region_id
			ORDER BY r.nom ASC, v.nom ASC
		');
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getById(int $id): ?array
	{
		$st = $this->db->prepare('SELECT id, nom, region_id, created_at FROM bngrc_villes WHERE id=?');
		$st->execute([$id]);
		$row = $st->fetch(PDO::FETCH_ASSOC);
		return $row ?: null;
	}

	public function create(int $regionId, string $nom): int
	{
		$nom = trim($nom);
		$st = $this->db->prepare('INSERT INTO bngrc_villes(region_id, nom) VALUES (?, ?)');
		$st->execute([$regionId, $nom]);
		return (int)$this->db->lastInsertId();
	}

	public function update(int $id, int $regionId, string $nom): void
	{
		$nom = trim($nom);
		$st = $this->db->prepare('UPDATE bngrc_villes SET region_id=?, nom=? WHERE id=?');
		$st->execute([$regionId, $nom, $id]);
	}

	public function delete(int $id): void
	{
		$st = $this->db->prepare('DELETE FROM bngrc_villes WHERE id=?');
		$st->execute([$id]);
	}
}
