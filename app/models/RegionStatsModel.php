<?php

namespace app\models;

use PDO;

class RegionStatsModel
{
	private PDO $db;

	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

	public function regionOptions(): array
	{
		$st = $this->db->query('SELECT id, nom FROM bngrc_regions ORDER BY nom ASC');
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}
}
