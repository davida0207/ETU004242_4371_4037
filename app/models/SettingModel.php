<?php

namespace app\models;

use PDO;

class SettingModel
{
	private PDO $db;

	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

	/**
	 * Récupérer une valeur par sa clé.
	 * Retourne la valeur par défaut si la clé n'existe pas.
	 */
	public function get(string $key, ?string $default = null): ?string
	{
		$st = $this->db->prepare('SELECT setting_value FROM bngrc_settings WHERE setting_key = ?');
		$st->execute([$key]);
		$val = $st->fetchColumn();
		return $val !== false ? (string)$val : $default;
	}

	/**
	 * Récupérer toutes les clés/valeurs.
	 */
	public function getAll(): array
	{
		$st = $this->db->query('SELECT setting_key, setting_value, label FROM bngrc_settings ORDER BY id ASC');
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Insérer ou mettre à jour une valeur.
	 */
	public function set(string $key, string $value, ?string $label = null): void
	{
		// Vérifier si la clé existe déjà
		$st = $this->db->prepare('SELECT id FROM bngrc_settings WHERE setting_key = ?');
		$st->execute([$key]);
		if ($st->fetchColumn() !== false) {
			$upd = $this->db->prepare('UPDATE bngrc_settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?');
			$upd->execute([$value, $key]);
		} else {
			$ins = $this->db->prepare('INSERT INTO bngrc_settings (setting_key, setting_value, label) VALUES (?, ?, ?)');
			$ins->execute([$key, $value, $label]);
		}
	}

	/**
	 * Raccourci : récupérer purchase_fee_percent (float).
	 * Fallback sur la config fichier si la clé DB n'existe pas.
	 */
	public function getPurchaseFeePercent(float $configFallback = 10.0): float
	{
		$val = $this->get('purchase_fee_percent');
		if ($val !== null && is_numeric($val)) {
			return (float)$val;
		}
		return $configFallback;
	}
}
