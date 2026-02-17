<?php

namespace app\services;

use app\models\AllocationModel;
use app\models\DispatchRunModel;
use PDO;

/**
 * Moteur de répartition des dons vers les besoins.
 *
 * Trois méthodes disponibles :
 *  - fifo          : Premier arrivé (date_besoin ASC) → premier servi
 *  - smallest      : Plus petit besoin d'abord (reste_besoin ASC)
 *  - proportional  : Répartition proportionnelle au prorata des restes
 */
class DispatchService
{
	private PDO $db;

	/** Méthodes autorisées */
	public const METHODS = ['fifo', 'smallest', 'proportional'];

	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

	/**
	 * Lance un dispatch complet selon la méthode choisie.
	 *
	 * @param string      $methode  fifo | smallest | proportional
	 * @param string|null $note     Note optionnelle pour le run
	 * @return array{run_id: int, nb_allocations: int, details: array}
	 */
	public function runDispatch(string $methode = 'fifo', ?string $note = null): array
	{
		if (!in_array($methode, self::METHODS, true)) {
			$methode = 'fifo';
		}

		$allocModel = new AllocationModel($this->db);
		$runModel   = new DispatchRunModel($this->db);

		$this->db->beginTransaction();

		try {
			// 1. Créer le run (avec la méthode)
			$runId = $runModel->create($note, $methode);

			// 2. Récupérer les dons non totalement attribués (date_don ASC, id ASC)
			$dons = $allocModel->unsatisfiedDons();

			$nbAllocations = 0;
			$details = [];

			foreach ($dons as &$don) {
				$resteDon = (float)$don['reste_don'];
				if ($resteDon <= 0) continue;

				// 3. Besoins ouverts pour le même article (tri selon méthode)
				$besoins = $allocModel->openBesoinsByArticle(
					(int)$don['article_id'],
					$methode
				);

				if (empty($besoins)) continue;

				// 4. Distribuer selon la méthode
				if ($methode === 'proportional') {
					$allocs = $this->distributeProportional($resteDon, $besoins);
				} else {
					// fifo et smallest : même algo séquentiel, seul l'ordre change
					$allocs = $this->distributeSequential($resteDon, $besoins);
				}

				foreach ($allocs as $al) {
					$allocModel->create($runId, (int)$don['id'], (int)$al['besoin_id'], $al['qty']);
					$nbAllocations++;

					$details[] = [
						'don_id'    => (int)$don['id'],
						'besoin_id' => (int)$al['besoin_id'],
						'article'   => $don['libelle'],
						'ville'     => $al['ville'],
						'region'    => $al['region'],
						'quantite'  => $al['qty'],
						'unite'     => $don['unite'],
					];
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

	/* =====================================================================
	 * Stratégie séquentielle (FIFO ou Smallest-first)
	 * On remplit les besoins un par un dans l'ordre fourni.
	 * ================================================================== */
	private function distributeSequential(float $resteDon, array $besoins): array
	{
		$allocs = [];
		foreach ($besoins as $b) {
			if ($resteDon <= 0) break;

			$resteBesoin = (float)$b['reste_besoin'];
			if ($resteBesoin <= 0) continue;

			$qty = min($resteDon, $resteBesoin);
			$allocs[] = [
				'besoin_id' => (int)$b['id'],
				'ville'     => $b['ville'],
				'region'    => $b['region'],
				'qty'       => $qty,
			];
			$resteDon -= $qty;
		}
		return $allocs;
	}

	/* =====================================================================
	 * Stratégie proportionnelle
	 * On répartit le don au prorata du reste de chaque besoin.
	 * Exemple : don = 100, besoin A reste 200, besoin B reste 300
	 *   total = 500  →  A reçoit 100*200/500 = 40, B reçoit 100*300/500 = 60
	 * ================================================================== */
	private function distributeProportional(float $resteDon, array $besoins): array
	{
		// Calculer le total des restes
		$totalReste = 0.0;
		foreach ($besoins as $b) {
			$totalReste += (float)$b['reste_besoin'];
		}

		if ($totalReste <= 0) return [];

		// Si le don couvre tout, on donne à chacun son reste complet
		if ($resteDon >= $totalReste) {
			$allocs = [];
			foreach ($besoins as $b) {
				$r = (float)$b['reste_besoin'];
				if ($r <= 0) continue;
				$allocs[] = [
					'besoin_id' => (int)$b['id'],
					'ville'     => $b['ville'],
					'region'    => $b['region'],
					'qty'       => $r,
				];
			}
			return $allocs;
		}

		// Répartition proportionnelle avec arrondi
		$allocs     = [];
		$distribue  = 0.0;
		$count      = count($besoins);
		$idx        = 0;

		foreach ($besoins as $b) {
			$idx++;
			$r = (float)$b['reste_besoin'];
			if ($r <= 0) continue;

			// Part proportionnelle
			$part = ($r / $totalReste) * $resteDon;

			// Arrondir à 2 décimales
			$part = round($part, 2);

			// Le dernier reçoit ce qui reste (évite erreurs d'arrondi)
			if ($idx === $count) {
				$part = round($resteDon - $distribue, 2);
			}

			// Ne pas dépasser le reste du besoin
			$part = min($part, $r);

			if ($part > 0) {
				$allocs[] = [
					'besoin_id' => (int)$b['id'],
					'ville'     => $b['ville'],
					'region'    => $b['region'],
					'qty'       => $part,
				];
				$distribue += $part;
			}
		}

		return $allocs;
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
