<?php

namespace app\models;

use PDO;

class ArticleModel
{
	private PDO $db;

	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

	public function listAll(?string $categorie = null): array
	{
		if ($categorie) {
			$st = $this->db->prepare('SELECT id, categorie, libelle, unite, prix_unitaire, actif FROM bngrc_articles WHERE categorie=? ORDER BY libelle ASC');
			$st->execute([$categorie]);
			return $st->fetchAll(PDO::FETCH_ASSOC);
		}

		$st = $this->db->query('SELECT id, categorie, libelle, unite, prix_unitaire, actif FROM bngrc_articles ORDER BY categorie ASC, libelle ASC');
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	public function listActive(?string $categorie = null): array
	{
		if ($categorie) {
			$st = $this->db->prepare('SELECT id, categorie, libelle, unite, prix_unitaire, actif FROM bngrc_articles WHERE actif=1 AND categorie=? ORDER BY libelle ASC');
			$st->execute([$categorie]);
			return $st->fetchAll(PDO::FETCH_ASSOC);
		}

		$st = $this->db->query('SELECT id, categorie, libelle, unite, prix_unitaire, actif FROM bngrc_articles WHERE actif=1 ORDER BY categorie ASC, libelle ASC');
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getById(int $id): ?array
	{
		$st = $this->db->prepare('SELECT id, categorie, libelle, unite, prix_unitaire, actif FROM bngrc_articles WHERE id=?');
		$st->execute([$id]);
		$row = $st->fetch(PDO::FETCH_ASSOC);
		return $row ?: null;
	}

	public function create(string $categorie, string $libelle, string $unite, float $prixUnitaire, bool $actif): int
	{
		$st = $this->db->prepare('INSERT INTO bngrc_articles(categorie, libelle, unite, prix_unitaire, actif) VALUES (?,?,?,?,?)');
		$st->execute([
			trim($categorie),
			trim($libelle),
			trim($unite),
			$prixUnitaire,
			$actif ? 1 : 0,
		]);
		return (int)$this->db->lastInsertId();
	}

	public function update(int $id, string $categorie, string $libelle, string $unite, float $prixUnitaire, bool $actif): void
	{
		$st = $this->db->prepare('UPDATE bngrc_articles SET categorie=?, libelle=?, unite=?, prix_unitaire=?, actif=? WHERE id=?');
		$st->execute([
			trim($categorie),
			trim($libelle),
			trim($unite),
			$prixUnitaire,
			$actif ? 1 : 0,
			$id,
		]);
	}

	public function deactivate(int $id): void
	{
		$st = $this->db->prepare('UPDATE bngrc_articles SET actif=0 WHERE id=?');
		$st->execute([$id]);
	}
}
