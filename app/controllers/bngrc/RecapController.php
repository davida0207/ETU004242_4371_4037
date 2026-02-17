<?php

namespace app\controllers\bngrc;

use app\models\BngrcDashboardModel;
use Flight;

class RecapController
{
	private function computeStats(): array
	{
		$rows = [];
		try {
			$model = new BngrcDashboardModel(Flight::db());
			$rows = $model->citySummary();
		} catch (\Throwable $e) {
			// garder rows vide
		}

		$totBesoins = 0.0;
		$totAttribue = 0.0;
		foreach ($rows as $r) {
			$besoins = (float)($r['besoins_valeur'] ?? 0);
			$attribue = (float)($r['attribue_valeur'] ?? 0);
			$totBesoins += $besoins;
			$totAttribue += $attribue;
		}
		$reste = max(0.0, $totBesoins - $totAttribue);
		return [
			'besoins_total' => $totBesoins,
			'attribue_total' => $totAttribue,
			'reste_total' => $reste,
		];
	}

	public function index(): void
	{
		$stats = $this->computeStats();
		Flight::render('bngrc/dashboard/recap', [
			'stats' => $stats,
		]);
	}

	public function data(): void
	{
		$stats = $this->computeStats();
		Flight::json($stats);
	}
}
