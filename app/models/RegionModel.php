<?php

namespace app\models;

use PDO;

class RegionModel
{
	private PDO $db;

	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

	public function listAll(): array
	{
		$st = $this->db->query('SELECT id, nom, created_at FROM bngrc_regions ORDER BY nom ASC');
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getById(int $id): ?array
	{
		$st = $this->db->prepare('SELECT id, nom, created_at FROM bngrc_regions WHERE id=?');
		$st->execute([$id]);
		$row = $st->fetch(PDO::FETCH_ASSOC);
		return $row ?: null;
	}

	public function create(string $nom): int
	{
		$nom = trim($nom);
		$st = $this->db->prepare('INSERT INTO bngrc_regions(nom) VALUES (?)');
		$st->execute([$nom]);
		return (int)$this->db->lastInsertId();
	}

	public function update(int $id, string $nom): void
	{
		$nom = trim($nom);
		$st = $this->db->prepare('UPDATE bngrc_regions SET nom=? WHERE id=?');
		$st->execute([$nom, $id]);
	}

	public function delete(int $id): void
	{
		$st = $this->db->prepare('DELETE FROM bngrc_regions WHERE id=?');
		$st->execute([$id]);
	}
}
