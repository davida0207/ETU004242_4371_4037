<?php

namespace app\controllers\bngrc;

use app\models\BngrcDashboardModel;
use Flight;

class DashboardController
{
	public function index(): void
	{
		$rows = [];
		$error = null;
		try {
			$model = new BngrcDashboardModel(Flight::db());
			$rows = $model->citySummary();
		} catch (\Throwable $e) {
			// Render the dashboard anyway (with 0 values) so the UI isn't blank.
			$error = $e->getMessage();
		}

		$totBesoins = 0.0;
		$totAttribue = 0.0;
		$villesAssistees = 0;

		foreach ($rows as $r) {
			$besoins = (float)($r['besoins_valeur'] ?? 0);
			$attribue = (float)($r['attribue_valeur'] ?? 0);
			$totBesoins += $besoins;
			$totAttribue += $attribue;
			if ($attribue > 0) {
				$villesAssistees++;
			}
		}

		$reste = max(0.0, $totBesoins - $totAttribue);
		$taux = $totBesoins > 0 ? ($totAttribue / $totBesoins) * 100.0 : 0.0;

		Flight::render('bngrc/dashboard/index', [
			'stats' => [
				'besoins_total' => $totBesoins,
				'attribue_total' => $totAttribue,
				'reste_total' => $reste,
				'taux_couverture' => $taux,
				'villes_assistees' => $villesAssistees,
				'villes_total' => count($rows),
			],
			'rows' => $rows,
			'error' => $error,
		]);
	}
}
