<?php

namespace app\controllers\bngrc;

use app\models\AllocationModel;
use app\models\DispatchRunModel;
use app\services\DispatchService;
use Flight;

class DispatchController
{
	/** Page principale de simulation avec stats + bouton Lancer */
	public function index(): void
	{
		$db = Flight::db();
		$allocModel = new AllocationModel($db);
		$runModel   = new DispatchRunModel($db);

		Flight::render('bngrc/dispatch/index', [
			'stats' => $allocModel->globalStats(),
			'runs'  => $runModel->listAll(),
			'flash' => (string)(Flight::request()->query['flash'] ?? ''),
		]);
	}

	/** POST — Exécute un dispatch complet */
	public function run(): void
	{
		$note    = trim((string)(Flight::request()->data->note ?? ''));
		$methode = (string)(Flight::request()->data->methode ?? 'fifo');

		// Valider la méthode
		if (!in_array($methode, DispatchService::METHODS, true)) {
			$methode = 'fifo';
		}

		$service = new DispatchService(Flight::db());

		try {
			$result = $service->runDispatch($methode, $note ?: null);

			if ($result['nb_allocations'] === 0) {
				Flight::redirect('/dispatch?flash=empty');
				return;
			}

			// Rediriger vers le détail du run créé
			Flight::redirect('/dispatch/runs/' . $result['run_id'] . '?flash=created');
		} catch (\Throwable $e) {
			Flight::redirect('/dispatch?flash=error');
		}
	}

	/** POST — Reset complet : supprimer toutes les allocations et tous les runs */
	public function reset(): void
	{
		$service = new DispatchService(Flight::db());

		try {
			$service->resetAll();
			Flight::redirect('/dispatch?flash=reset');
		} catch (\Throwable $e) {
			Flight::redirect('/dispatch?flash=error');
		}
	}

	/** Liste de tous les dispatch runs (historique) */
	public function runs(): void
	{
		$runModel = new DispatchRunModel(Flight::db());

		Flight::render('bngrc/dispatch/runs', [
			'runs'  => $runModel->listAll(),
			'flash' => (string)(Flight::request()->query['flash'] ?? ''),
		]);
	}

	/** Détail d'un run (allocations) */
	public function showRun(int $id): void
	{
		$runModel = new DispatchRunModel(Flight::db());
		$run = $runModel->getById($id);

		if (!$run) {
			Flight::notFound();
			return;
		}

		$allocations = $runModel->allocationsForRun($id, $run['methode'] ?? 'fifo');

		Flight::render('bngrc/dispatch/run_detail', [
			'run'         => $run,
			'allocations' => $allocations,
			'flash'       => (string)(Flight::request()->query['flash'] ?? ''),
		]);
	}

	/** POST — Supprimer un run et ses allocations */
	public function deleteRun(int $id): void
	{
		$db = Flight::db();
		$runModel = new DispatchRunModel($db);

		$db->beginTransaction();
		try {
			$runModel->deleteRunAllocations($id);
			$db->commit();
			Flight::redirect('/dispatch/runs?flash=deleted');
		} catch (\Throwable $e) {
			$db->rollBack();
			Flight::redirect('/dispatch/runs?flash=error');
		}
	}
}
