<?php

namespace app\controllers\bngrc;

use app\models\ArticleModel;
use app\models\DonModel;
use Flight;

class DonController
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
			'categorie' => (string)($q['categorie'] ?? ''),
			'article_id' => (string)($q['article_id'] ?? ''),
			'start_date' => (string)($q['start_date'] ?? ''),
			'end_date' => (string)($q['end_date'] ?? ''),
		];

		$model = new DonModel(Flight::db());
		$rows = $model->listWithFilters($filters);

		$articles = (new ArticleModel(Flight::db()))->listAll($filters['categorie'] !== '' ? $filters['categorie'] : null);

		Flight::render('bngrc/dons/index', [
			'rows' => $rows,
			'filters' => $filters,
			'articles' => $articles,
			'categories' => $this->categorieOptions(),
			'flash' => (string)($q['flash'] ?? ''),
		]);
	}

	public function addForm(): void
	{
		$articles = (new ArticleModel(Flight::db()))->listActive();
		Flight::render('bngrc/dons/form', [
			'mode' => 'add',
			'don' => [
				'article_id' => '',
				'quantite' => '',
				'date_don' => date('Y-m-d'),
				'source' => '',
				'note' => '',
			],
			'articles' => $articles,
			'errors' => ['article_id' => '', 'quantite' => '', 'date_don' => ''],
		]);
	}

	public function addPost(): void
	{
		$req = Flight::request();
		$articleId = (string)($req->data->article_id ?? '');
		$quantite = (string)($req->data->quantite ?? '');
		$dateDon = (string)($req->data->date_don ?? '');
		$source = (string)($req->data->source ?? '');
		$note = (string)($req->data->note ?? '');

		$errors = ['article_id' => '', 'quantite' => '', 'date_don' => ''];
		if ($articleId === '' || (int)$articleId <= 0) {
			$errors['article_id'] = 'L\'article est obligatoire.';
		}
		if (!is_numeric($quantite) || (float)$quantite <= 0) {
			$errors['quantite'] = 'La quantité doit être > 0.';
		}
		if ($dateDon === '') {
			$errors['date_don'] = 'La date est obligatoire.';
		}

		$articles = (new ArticleModel(Flight::db()))->listActive();
		$articleIds = array_map(fn($a) => (int)$a['id'], $articles);
		if ($articleId !== '' && !in_array((int)$articleId, $articleIds, true)) {
			$errors['article_id'] = 'Article inactif ou invalide.';
		}

		if ($errors['article_id'] || $errors['quantite'] || $errors['date_don']) {
			Flight::render('bngrc/dons/form', [
				'mode' => 'add',
				'don' => [
					'article_id' => $articleId,
					'quantite' => $quantite,
					'date_don' => $dateDon,
					'source' => $source,
					'note' => $note,
				],
				'articles' => $articles,
				'errors' => $errors,
			]);
			return;
		}

		$model = new DonModel(Flight::db());
		$id = $model->create((int)$articleId, (float)$quantite, $dateDon, $source !== '' ? $source : null, $note !== '' ? $note : null);
		Flight::redirect('/dons?flash=created#d' . $id);
	}

	public function show(int $id): void
	{
		$model = new DonModel(Flight::db());
		$don = $model->getById($id);
		if (!$don) {
			Flight::notFound();
			return;
		}

		$allocations = $model->allocationsForDon($id);
		Flight::render('bngrc/dons/show', [
			'don' => $don,
			'allocations' => $allocations,
			'canEdit' => !$model->hasAllocations($id),
			'canDelete' => !$model->hasAllocations($id),
		]);
	}

	public function editForm(int $id): void
	{
		$model = new DonModel(Flight::db());
		$don = $model->getById($id);
		if (!$don) {
			Flight::notFound();
			return;
		}

		if ($model->hasAllocations($id)) {
			Flight::redirect('/dons/' . $id);
			return;
		}

		$articles = (new ArticleModel(Flight::db()))->listActive();
		Flight::render('bngrc/dons/form', [
			'mode' => 'edit',
			'don' => $don,
			'articles' => $articles,
			'errors' => ['article_id' => '', 'quantite' => '', 'date_don' => ''],
		]);
	}

	public function editPost(int $id): void
	{
		$model = new DonModel(Flight::db());
		$current = $model->getById($id);
		if (!$current) {
			Flight::notFound();
			return;
		}
		if ($model->hasAllocations($id)) {
			Flight::redirect('/dons/' . $id);
			return;
		}

		$req = Flight::request();
		$articleId = (string)($req->data->article_id ?? '');
		$quantite = (string)($req->data->quantite ?? '');
		$dateDon = (string)($req->data->date_don ?? '');
		$source = (string)($req->data->source ?? '');
		$note = (string)($req->data->note ?? '');

		$errors = ['article_id' => '', 'quantite' => '', 'date_don' => ''];
		if ($articleId === '' || (int)$articleId <= 0) {
			$errors['article_id'] = 'L\'article est obligatoire.';
		}
		if (!is_numeric($quantite) || (float)$quantite <= 0) {
			$errors['quantite'] = 'La quantité doit être > 0.';
		}
		if ($dateDon === '') {
			$errors['date_don'] = 'La date est obligatoire.';
		}

		$articles = (new ArticleModel(Flight::db()))->listActive();
		$articleIds = array_map(fn($a) => (int)$a['id'], $articles);
		if ($articleId !== '' && !in_array((int)$articleId, $articleIds, true)) {
			$errors['article_id'] = 'Article inactif ou invalide.';
		}

		if ($errors['article_id'] || $errors['quantite'] || $errors['date_don']) {
			Flight::render('bngrc/dons/form', [
				'mode' => 'edit',
				'don' => [
					'id' => $id,
					'article_id' => $articleId,
					'quantite' => $quantite,
					'date_don' => $dateDon,
					'source' => $source,
					'note' => $note,
				],
				'articles' => $articles,
				'errors' => $errors,
			]);
			return;
		}

		$model->update($id, (int)$articleId, (float)$quantite, $dateDon, $source !== '' ? $source : null, $note !== '' ? $note : null);
		Flight::redirect('/dons/' . $id);
	}

	public function deletePost(int $id): void
	{
		$model = new DonModel(Flight::db());
		$don = $model->getById($id);
		if (!$don) {
			Flight::notFound();
			return;
		}

		if ($model->hasAllocations($id)) {
			Flight::redirect('/dons?flash=blocked');
			return;
		}

		$model->delete($id);
		Flight::redirect('/dons?flash=deleted');
	}
}
