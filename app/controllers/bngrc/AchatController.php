<?php

namespace app\controllers\bngrc;

use app\models\AchatModel;
use app\models\BesoinModel;
use app\models\RegionModel;
use app\models\SettingModel;
use app\models\VilleModel;
use Flight;
use PDO;

class AchatController
{
	public function index(): void
	{
		$q = Flight::request()->query;
		$filters = [
			'ville_id' => (string)($q['ville_id'] ?? ''),
			'start_date' => (string)($q['start_date'] ?? ''),
			'end_date' => (string)($q['end_date'] ?? ''),
		];

		$db = Flight::db();
		$model = new AchatModel($db);
		$rows = $model->listWithFilters($filters);

		$regions = (new RegionModel($db))->listAll();
		$villes = (new VilleModel($db))->listAll();

		// Calcul des fonds en argent restants
		$cashInfo = $this->cashSummary($db, $model);

		Flight::render('bngrc/achats/index', [
			'rows' => $rows,
			'filters' => $filters,
			'villes' => $villes,
			'regions' => $regions,
			'cashInfo' => $cashInfo,
			'flash' => (string)($q['flash'] ?? ''),
		]);
	}

	public function addForm(): void
	{
		$db = Flight::db();
		$req = Flight::request();
		$besoinId = (int)($req->query->besoin_id ?? 0);
		$besoin = null;
		if ($besoinId > 0) {
			$besoin = (new BesoinModel($db))->getById($besoinId);
		}

		// Charger la liste des besoins encore ouverts pour pouvoir choisir
		$besoinModel = new BesoinModel($db);
		$allBesoins = $besoinModel->listWithFilters([]);
		$besoinsOuverts = [];
		foreach ($allBesoins as $b) {
			$quantite = (float)($b['quantite'] ?? 0);
			$attribue = (float)($b['attribue_quantite'] ?? 0);
			if ($quantite - $attribue > 0.000001) {
				$besoinsOuverts[] = $b;
			}
		}

		$villes = (new VilleModel($db))->listAll();

		$achat = [
			'besoin_id' => $besoinId ?: '',
			'ville_id' => $besoin['ville_id'] ?? '',
			'quantite' => '',
			'date_achat' => date('Y-m-d'),
			'note' => '',
		];

		$settingModel = new SettingModel($db);
		$configFallback = (float)(Flight::get('config')['bngrc']['purchase_fee_percent'] ?? 10.0);
		$fraisPercentDb = $settingModel->getPurchaseFeePercent($configFallback);
		$postedFrais = (string)($req->data->frais_percent ?? '');
		if ($postedFrais !== '') {
			$f = is_numeric($postedFrais) ? (float)$postedFrais : $fraisPercentDb;
			if ($f < 0.0) $f = 0.0;
			if ($f > 100.0) $f = $fraisPercentDb;
			$fraisPercent = $f;
		} else {
			$fraisPercent = $fraisPercentDb;
		}
		$cashInfo = $this->cashSummary($db, new AchatModel($db));

		Flight::render('bngrc/achats/form', [
			'mode' => 'add',
			'achat' => $achat,
			'besoin' => $besoin,
			'besoins_ouverts' => $besoinsOuverts,
			'villes' => $villes,
			'frais_percent' => $fraisPercent,
			'cashInfo' => $cashInfo,
			'errors' => ['ville_id' => '', 'quantite' => '', 'date_achat' => '', 'cash' => ''],
		]);
	}

	public function addPost(): void
	{
		$db = Flight::db();
		$req = Flight::request();
		$besoinId = (int)($req->data->besoin_id ?? 0);
		$villeId = (int)($req->data->ville_id ?? 0);
		$quantite = (string)($req->data->quantite ?? '');
		$dateAchat = (string)($req->data->date_achat ?? '');
		$note = (string)($req->data->note ?? '');

		$errors = ['ville_id' => '', 'quantite' => '', 'date_achat' => '', 'cash' => ''];
		$besoin = null;
		if ($besoinId > 0) {
			$besoin = (new BesoinModel($db))->getById($besoinId);
		}

		if ($villeId <= 0) {
			$errors['ville_id'] = 'La ville est obligatoire.';
		}
		if (!is_numeric($quantite) || (float)$quantite <= 0) {
			$errors['quantite'] = 'La quantité doit être > 0.';
		}
		if ($dateAchat === '') {
			$errors['date_achat'] = "La date d'achat est obligatoire.";
		}

		if ($besoin === null) {
			$errors['quantite'] = 'Besoin introuvable.';
		}

		// Vérifier qu'on ne dépasse pas le besoin restant
		$resteQ = 0.0;
		$articleId = 0;
		$prixUnitaire = 0.0;
		if ($besoin !== null) {
			$quantiteTotale = (float)$besoin['quantite'];
			$attribueQ = (float)$besoin['attribue_quantite'];
			$resteQ = max(0.0, $quantiteTotale - $attribueQ);
			$articleId = (int)$besoin['article_id'];
			$prixUnitaire = (float)$besoin['prix_unitaire'];
			if (is_numeric($quantite) && (float)$quantite > $resteQ + 1e-6) {
				$errors['quantite'] = 'La quantité dépasse le besoin restant.';
			}
		}

		$settingModel = new SettingModel($db);
		$configFallback = (float)(Flight::get('config')['bngrc']['purchase_fee_percent'] ?? 10.0);
		$fraisPercentDb = $settingModel->getPurchaseFeePercent($configFallback);
		$postedFrais = (string)($req->data->frais_percent ?? '');
		if ($postedFrais !== '') {
			$f = is_numeric($postedFrais) ? (float)$postedFrais : $fraisPercentDb;
			if ($f < 0.0) $f = 0.0;
			if ($f > 100.0) $f = $fraisPercentDb;
			$fraisPercent = $f;
		} else {
			$fraisPercent = $fraisPercentDb;
		}
		$quantiteF = is_numeric($quantite) ? (float)$quantite : 0.0;
		$montantBase = $quantiteF * $prixUnitaire;
		$montantTotal = $montantBase * (1.0 + $fraisPercent / 100.0);

		$achatModel = new AchatModel($db);
		$cashInfo = $this->cashSummary($db, $achatModel);
		if ($montantTotal > $cashInfo['cash_restant'] + 1e-6) {
			$errors['cash'] = 'Fonds en argent insuffisants pour cet achat.';
		}

		$villes = (new VilleModel($db))->listAll();
		// Recharger la liste des besoins ouverts pour réafficher en cas d'erreur
		$besoinModel = new BesoinModel($db);
		$allBesoins = $besoinModel->listWithFilters([]);
		$besoinsOuverts = [];
		foreach ($allBesoins as $b) {
			$quantiteB = (float)($b['quantite'] ?? 0);
			$attribueB = (float)($b['attribue_quantite'] ?? 0);
			if ($quantiteB - $attribueB > 0.000001) {
				$besoinsOuverts[] = $b;
			}
		}
		$achatData = [
			'besoin_id' => $besoinId ?: '',
			'ville_id' => $villeId ?: '',
			'quantite' => $quantite,
			'date_achat' => $dateAchat,
			'note' => $note,
		];

		if ($errors['ville_id'] || $errors['quantite'] || $errors['date_achat'] || $errors['cash']) {
			Flight::render('bngrc/achats/form', [
				'mode' => 'add',
				'achat' => $achatData,
				'besoin' => $besoin,
				'besoins_ouverts' => $besoinsOuverts,
				'villes' => $villes,
				'frais_percent' => $fraisPercent,
				'cashInfo' => $cashInfo,
				'errors' => $errors,
			]);
			return;
		}

		// Tout est OK: on enregistre l'achat + on crée un don en nature/matériau correspondant.
		/** @var PDO $db */
		$db->beginTransaction();
		try {
			$achatId = $achatModel->insert($besoinId, $villeId, $articleId, $quantiteF, $montantBase, $fraisPercent, $montantTotal, $dateAchat, $note !== '' ? $note : null);

			// Créer un don correspondant aux biens achetés
			$donModel = new \app\models\DonModel($db);
			$donId = $donModel->create($articleId, $quantiteF, $dateAchat, 'Achat via dons en argent', 'Achat #' . $achatId);

			$db->commit();
		} catch (\Throwable $e) {
			$db->rollBack();
			throw $e;
		}

		Flight::redirect('/achats?flash=created');
	}

	private function cashSummary(PDO $db, AchatModel $achatModel): array
	{
		// Total dons en argent
		$stArticle = $db->query("SELECT id FROM bngrc_articles WHERE categorie='argent' LIMIT 1");
		$articleId = (int)($stArticle->fetchColumn() ?: 0);
		$totalArgent = 0.0;
		if ($articleId > 0) {
			$stCash = $db->prepare('SELECT COALESCE(SUM(quantite),0) FROM bngrc_dons WHERE article_id = ?');
			$stCash->execute([$articleId]);
			$totalArgent = (float)$stCash->fetchColumn();
		}
		$depense = $achatModel->totalSpent();
		$restant = max(0.0, $totalArgent - $depense);

		return [
			'total_dons_argent' => $totalArgent,
			'total_achats' => $depense,
			'cash_restant' => $restant,
		];
	}
}
