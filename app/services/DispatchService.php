<?php

namespace app\services;

use app\models\AllocationModel;
use app\models\DispatchRunModel;
use PDO;

/**
 * Moteur de répartition des dons vers les besoins.
 *
 * Algorithme :
 *  1. Trier les dons par date_don ASC, id ASC
 *  2. Pour chaque don ayant un reste > 0, trouver les besoins ouverts du MÊME article
 *     triés par date_besoin ASC, id ASC
 *  3. Créer une allocation de min(reste_don, reste_besoin)
 *  4. Le tout dans un dispatch_run, encapsulé dans une transaction SQL
 */
class DispatchService
{
	private PDO $db;

	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

	/**
	 * Lance un dispatch complet.
	 *
	 * @param string|null $note Note optionnelle pour le run
	 * @return array{run_id: int, nb_allocations: int, details: array}
	 * @throws \RuntimeException si aucune allocation créée
	 */
	public function runDispatch(?string $note = null): array
	{
		$allocModel = new AllocationModel($this->db);
		$runModel   = new DispatchRunModel($this->db);

		$this->db->beginTransaction();

		try {
			// 1. Créer le run
			$runId = $runModel->create($note);

			// 2. Récupérer les dons non totalement attribués (date_don ASC, id ASC)
			$dons = $allocModel->unsatisfiedDons();

			$nbAllocations = 0;
			$details = [];

			foreach ($dons as &$don) {
				$resteDon = (float)$don['reste_don'];
				if ($resteDon <= 0) continue;

				// 3. Besoins ouverts pour le même article (date_besoin ASC, id ASC)
				$besoins = $allocModel->openBesoinsByArticle((int)$don['article_id']);

				foreach ($besoins as $besoin) {
					if ($resteDon <= 0) break;

					$resteBesoin = (float)$besoin['reste_besoin'];
					if ($resteBesoin <= 0) continue;

					// 4. Quantité à allouer = min(reste_don, reste_besoin)
					$qty = min($resteDon, $resteBesoin);

					$allocModel->create($runId, (int)$don['id'], (int)$besoin['id'], $qty);
					$nbAllocations++;

					$details[] = [
						'don_id'    => (int)$don['id'],
						'besoin_id' => (int)$besoin['id'],
						'article'   => $don['libelle'],
						'ville'     => $besoin['ville'],
						'region'    => $besoin['region'],
						'quantite'  => $qty,
						'unite'     => $don['unite'],
					];

					$resteDon -= $qty;
				}
			}
			unset($don);

			$this->db->commit();

			return [
				'run_id'         => $runId,
				'nb_allocations' => $nbAllocations,
				'details'        => $details,
			];

		} catch (\Throwable $e) {
			$this->db->rollBack();
			throw new \RuntimeException('Erreur lors du dispatch : ' . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * Réinitialise toutes les allocations et tous les runs.
	 * @return int Nombre d'allocations supprimées
	 */
	public function resetAll(): int
	{
		$this->db->beginTransaction();
		try {
			$runModel = new DispatchRunModel($this->db);
			$count = $runModel->resetAll();
			$this->db->commit();
			return $count;
		} catch (\Throwable $e) {
			$this->db->rollBack();
			throw new \RuntimeException('Erreur lors du reset : ' . $e->getMessage(), 0, $e);
		}
	}
}
