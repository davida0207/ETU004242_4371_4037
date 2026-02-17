<?php

namespace app\controllers\bngrc;

use app\models\BngrcDashboardModel;
use Flight;

class DashboardController
{
	public function index(): void
	{
		$model = new BngrcDashboardModel(Flight::db());
		$rows = $model->citySummary();
		$donStats = $model->donStats();

		/* --- Agrégation besoins / attribué par ville --- */
		$totBesoins = 0.0;
		$totAttribue = 0.0;
		$totBesoinsQ = 0.0;
		$villesAssistees = 0;

		foreach ($rows as $r) {
			$besoins  = (float)($r['besoins_valeur'] ?? 0);
			$attribue = (float)($r['attribue_valeur'] ?? 0);
			$totBesoins  += $besoins;
			$totAttribue += $attribue;
			$totBesoinsQ += (float)($r['besoins_quantite'] ?? 0);
			if ($attribue > 0) {
				$villesAssistees++;
			}
		}

		$reste = max(0.0, $totBesoins - $totAttribue);
		$taux  = $totBesoins > 0 ? ($totAttribue / $totBesoins) * 100.0 : 0.0;
		$villesTotalCount = count($rows);

		/* --- Tri et recherche côté serveur --- */
		$search = trim((string)(Flight::request()->query['q'] ?? ''));
		$sort   = (string)(Flight::request()->query['sort'] ?? '');

		// Filtrer par nom de ville
		if ($search !== '') {
			$rows = array_filter($rows, function ($r) use ($search) {
				return stripos($r['ville'], $search) !== false
					|| stripos($r['region'], $search) !== false;
			});
			$rows = array_values($rows);
		}

		// Trier
		if ($sort === 'besoins') {
			usort($rows, fn($a, $b) => (float)$b['besoins_valeur'] <=> (float)$a['besoins_valeur']);
		} elseif ($sort === 'reste') {
			usort($rows, function ($a, $b) {
				$resteA = (float)$a['besoins_valeur'] - (float)$a['attribue_valeur'];
				$resteB = (float)$b['besoins_valeur'] - (float)$b['attribue_valeur'];
				return $resteB <=> $resteA;
			});
		} elseif ($sort === 'alpha') {
			usort($rows, fn($a, $b) => strcmp($a['ville'], $b['ville']));
		}

		Flight::render('bngrc/dashboard/index', [
			'stats' => [
				'besoins_total'      => $totBesoins,
				'besoins_quantite'   => $totBesoinsQ,
				'attribue_total'     => $totAttribue,
				'reste_total'        => $reste,
				'taux_couverture'    => $taux,
				'villes_assistees'   => $villesAssistees,
				'villes_total'       => $villesTotalCount,
				'dons_total_valeur'  => $donStats['dons_total_valeur'],
				'dons_total_count'   => $donStats['dons_total_count'],
				'dons_attribue_valeur' => $donStats['dons_attribue_valeur'],
			],
			'rows'   => $rows,
			'search' => $search,
			'sort'   => $sort,
		]);
	}
}
