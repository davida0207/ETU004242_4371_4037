<?php

namespace app\controllers\bngrc;

use app\models\ArticleModel;
use app\models\BesoinModel;
use app\models\RegionModel;
use app\models\VilleModel;
use Flight;

class BesoinController
{
	private function categorieOptions(): array
	{
		return [
			'' => 'Toutes',
			'nature' => 'Nature',
			'materiau' => 'Matériau',
			'argent' => 'Argent',
		];
	}

	public function index(): void
	{
		$q = Flight::request()->query;
		$filters = [
			'region_id' => (string)($q['region_id'] ?? ''),
			'ville_id' => (string)($q['ville_id'] ?? ''),
			'categorie' => (string)($q['categorie'] ?? ''),
			'article_id' => (string)($q['article_id'] ?? ''),
			'start_date' => (string)($q['start_date'] ?? ''),
			'end_date' => (string)($q['end_date'] ?? ''),
		];

		$model = new BesoinModel(Flight::db());
		$rows = $model->listWithFilters($filters);

		$regions = (new RegionModel(Flight::db()))->listAll();
		$villes = (new VilleModel(Flight::db()))->listAll(!empty($filters['region_id']) ? (int)$filters['region_id'] : null);
		$articles = (new ArticleModel(Flight::db()))->listAll($filters['categorie'] !== '' ? $filters['categorie'] : null);

		Flight::render('bngrc/besoins/index', [
			'rows' => $rows,
			'filters' => $filters,
			'regions' => $regions,
			'villes' => $villes,
			'articles' => $articles,
			'categories' => $this->categorieOptions(),
			'flash' => (string)($q['flash'] ?? ''),
		]);
	}

	public function addForm(): void
	{
		$villes = (new VilleModel(Flight::db()))->listAll();
		$articles = (new ArticleModel(Flight::db()))->listActive();

		Flight::render('bngrc/besoins/form', [
			'mode' => 'add',
			'besoin' => [
				'ville_id' => '',
				'article_id' => '',
				'quantite' => '',
				'date_besoin' => date('Y-m-d'),
				'note' => '',
			],
			'villes' => $villes,
			'articles' => $articles,
			'errors' => ['ville_id' => '', 'article_id' => '', 'quantite' => '', 'date_besoin' => ''],
		]);
	}

	public function addPost(): void
	{
		$req = Flight::request();
		$villeId = (string)($req->data->ville_id ?? '');
		$articleId = (string)($req->data->article_id ?? '');
		$quantite = (string)($req->data->quantite ?? '');
		$dateBesoin = (string)($req->data->date_besoin ?? '');
		$note = (string)($req->data->note ?? '');

		$errors = ['ville_id' => '', 'article_id' => '', 'quantite' => '', 'date_besoin' => ''];
		if ($villeId === '' || (int)$villeId <= 0) {
			$errors['ville_id'] = 'La ville est obligatoire.';
		}
		if ($articleId === '' || (int)$articleId <= 0) {
			$errors['article_id'] = 'L\'article est obligatoire.';
		}
		if (!is_numeric($quantite) || (float)$quantite <= 0) {
			$errors['quantite'] = 'La quantité doit être > 0.';
		}
		if ($dateBesoin === '') {
			$errors['date_besoin'] = 'La date est obligatoire.';
		}

		$articles = (new ArticleModel(Flight::db()))->listActive();
		$articleIds = array_map(fn($a) => (int)$a['id'], $articles);
		if ($articleId !== '' && !in_array((int)$articleId, $articleIds, true)) {
			$errors['article_id'] = 'Article inactif ou invalide.';
		}

		$villes = (new VilleModel(Flight::db()))->listAll();
		if ($errors['ville_id'] || $errors['article_id'] || $errors['quantite'] || $errors['date_besoin']) {
			Flight::render('bngrc/besoins/form', [
				'mode' => 'add',
				'besoin' => [
					'ville_id' => $villeId,
					'article_id' => $articleId,
					'quantite' => $quantite,
					'date_besoin' => $dateBesoin,
					'note' => $note,
				],
				'villes' => $villes,
				'articles' => $articles,
				'errors' => $errors,
			]);
			return;
		}

		$model = new BesoinModel(Flight::db());
		$id = $model->create((int)$villeId, (int)$articleId, (float)$quantite, $dateBesoin, $note !== '' ? $note : null);
		Flight::redirect('/besoins?flash=created#b' . $id);
	}

	public function show(int $id): void
	{
		$model = new BesoinModel(Flight::db());
		$besoin = $model->getById($id);
		if (!$besoin || $besoin['deleted_at'] !== null) {
			Flight::notFound();
			return;
		}

		$allocations = $model->allocationsForBesoin($id);
		Flight::render('bngrc/besoins/show', [
			'besoin' => $besoin,
			'allocations' => $allocations,
			'canDelete' => !$model->hasAllocations($id),
		]);
	}

	public function editForm(int $id): void
	{
		$model = new BesoinModel(Flight::db());
		$besoin = $model->getById($id);
		if (!$besoin || $besoin['deleted_at'] !== null) {
			Flight::notFound();
			return;
		}

		$villes = (new VilleModel(Flight::db()))->listAll();
		$articles = (new ArticleModel(Flight::db()))->listActive();

		Flight::render('bngrc/besoins/form', [
			'mode' => 'edit',
			'besoin' => $besoin,
			'villes' => $villes,
			'articles' => $articles,
			'errors' => ['ville_id' => '', 'article_id' => '', 'quantite' => '', 'date_besoin' => ''],
		]);
	}

	public function editPost(int $id): void
	{
		$model = new BesoinModel(Flight::db());
		$current = $model->getById($id);
		if (!$current || $current['deleted_at'] !== null) {
			Flight::notFound();
			return;
		}

		$req = Flight::request();
		$villeId = (string)($req->data->ville_id ?? '');
		$articleId = (string)($req->data->article_id ?? '');
		$quantite = (string)($req->data->quantite ?? '');
		$dateBesoin = (string)($req->data->date_besoin ?? '');
		$note = (string)($req->data->note ?? '');

		$errors = ['ville_id' => '', 'article_id' => '', 'quantite' => '', 'date_besoin' => ''];
		if ($villeId === '' || (int)$villeId <= 0) {
			$errors['ville_id'] = 'La ville est obligatoire.';
		}
		if ($articleId === '' || (int)$articleId <= 0) {
			$errors['article_id'] = 'L\'article est obligatoire.';
		}
		if (!is_numeric($quantite) || (float)$quantite <= 0) {
			$errors['quantite'] = 'La quantité doit être > 0.';
		}
		if ($dateBesoin === '') {
			$errors['date_besoin'] = 'La date est obligatoire.';
		}

		$articles = (new ArticleModel(Flight::db()))->listActive();
		$articleIds = array_map(fn($a) => (int)$a['id'], $articles);
		if ($articleId !== '' && !in_array((int)$articleId, $articleIds, true)) {
			$errors['article_id'] = 'Article inactif ou invalide.';
		}

		$villes = (new VilleModel(Flight::db()))->listAll();
		if ($errors['ville_id'] || $errors['article_id'] || $errors['quantite'] || $errors['date_besoin']) {
			Flight::render('bngrc/besoins/form', [
				'mode' => 'edit',
				'besoin' => [
					'id' => $id,
					'ville_id' => $villeId,
					'article_id' => $articleId,
					'quantite' => $quantite,
					'date_besoin' => $dateBesoin,
					'note' => $note,
				],
				'villes' => $villes,
				'articles' => $articles,
				'errors' => $errors,
			]);
			return;
		}

		$model->update($id, (int)$villeId, (int)$articleId, (float)$quantite, $dateBesoin, $note !== '' ? $note : null);
		Flight::redirect('/besoins/' . $id);
	}

	public function deletePost(int $id): void
	{
		$model = new BesoinModel(Flight::db());
		$besoin = $model->getById($id);
		if (!$besoin || $besoin['deleted_at'] !== null) {
			Flight::notFound();
			return;
		}

		if ($model->hasAllocations($id)) {
			Flight::redirect('/besoins?flash=blocked');
			return;
		}

		$model->softDelete($id);
		Flight::redirect('/besoins?flash=deleted');
	}
}
