<?php

namespace app\controllers\bngrc;

use app\models\ArticleModel;
use Flight;
use PDOException;

class ArticleController
{
	private function categorieOptions(): array
	{
		return [
			'nature' => 'Nature',
			'materiau' => 'Matériau',
			'argent' => 'Argent',
		];
	}

	public function index(): void
	{
		$categorie = (string)(Flight::request()->query['categorie'] ?? '');
		$categorie = $categorie !== '' ? $categorie : null;

		$model = new ArticleModel(Flight::db());
		Flight::render('bngrc/articles/index', [
			'articles' => $model->listAll($categorie),
			'filters' => ['categorie' => $categorie],
			'categories' => $this->categorieOptions(),
			'flash' => Flight::request()->query['flash'] ?? null,
		]);
	}

	public function addForm(): void
	{
		Flight::render('bngrc/articles/form', [
			'mode' => 'add',
			'article' => ['categorie' => 'nature', 'libelle' => '', 'unite' => '', 'prix_unitaire' => '0', 'actif' => 1],
			'categories' => $this->categorieOptions(),
			'errors' => ['categorie' => '', 'libelle' => '', 'unite' => '', 'prix_unitaire' => ''],
		]);
	}

	public function addPost(): void
	{
		$req = Flight::request();
		$categorie = (string)($req->data->categorie ?? '');
		$libelle = (string)($req->data->libelle ?? '');
		$unite = (string)($req->data->unite ?? '');
		$prix = (string)($req->data->prix_unitaire ?? '0');
		$actif = (string)($req->data->actif ?? '1');

		$errors = ['categorie' => '', 'libelle' => '', 'unite' => '', 'prix_unitaire' => ''];
		if (!array_key_exists($categorie, $this->categorieOptions())) {
			$errors['categorie'] = 'Catégorie invalide.';
		}
		if (trim($libelle) === '') {
			$errors['libelle'] = "Le libellé est obligatoire.";
		}
		if (trim($unite) === '') {
			$errors['unite'] = "L'unité est obligatoire.";
		}
		if (!is_numeric($prix) || (float)$prix < 0) {
			$errors['prix_unitaire'] = 'Le prix unitaire doit être un nombre positif.';
		}

		if ($errors['categorie'] || $errors['libelle'] || $errors['unite'] || $errors['prix_unitaire']) {
			Flight::render('bngrc/articles/form', [
				'mode' => 'add',
				'article' => [
					'categorie' => $categorie,
					'libelle' => $libelle,
					'unite' => $unite,
					'prix_unitaire' => $prix,
					'actif' => $actif === '1' ? 1 : 0,
				],
				'categories' => $this->categorieOptions(),
				'errors' => $errors,
			]);
			return;
		}

		try {
			$model = new ArticleModel(Flight::db());
			$model->create($categorie, $libelle, $unite, (float)$prix, $actif === '1');
			Flight::redirect('/articles?flash=created');
		} catch (PDOException $e) {
			$errors['libelle'] = 'Impossible de créer (déjà existant ?).';
			Flight::render('bngrc/articles/form', [
				'mode' => 'add',
				'article' => [
					'categorie' => $categorie,
					'libelle' => $libelle,
					'unite' => $unite,
					'prix_unitaire' => $prix,
					'actif' => $actif === '1' ? 1 : 0,
				],
				'categories' => $this->categorieOptions(),
				'errors' => $errors,
			]);
		}
	}

	public function editForm(int $id): void
	{
		$model = new ArticleModel(Flight::db());
		$article = $model->getById($id);
		if (!$article) {
			Flight::notFound();
			return;
		}

		Flight::render('bngrc/articles/form', [
			'mode' => 'edit',
			'article' => $article,
			'categories' => $this->categorieOptions(),
			'errors' => ['categorie' => '', 'libelle' => '', 'unite' => '', 'prix_unitaire' => ''],
		]);
	}

	public function editPost(int $id): void
	{
		$req = Flight::request();
		$categorie = (string)($req->data->categorie ?? '');
		$libelle = (string)($req->data->libelle ?? '');
		$unite = (string)($req->data->unite ?? '');
		$prix = (string)($req->data->prix_unitaire ?? '0');
		$actif = (string)($req->data->actif ?? '1');

		$errors = ['categorie' => '', 'libelle' => '', 'unite' => '', 'prix_unitaire' => ''];
		if (!array_key_exists($categorie, $this->categorieOptions())) {
			$errors['categorie'] = 'Catégorie invalide.';
		}
		if (trim($libelle) === '') {
			$errors['libelle'] = "Le libellé est obligatoire.";
		}
		if (trim($unite) === '') {
			$errors['unite'] = "L'unité est obligatoire.";
		}
		if (!is_numeric($prix) || (float)$prix < 0) {
			$errors['prix_unitaire'] = 'Le prix unitaire doit être un nombre positif.';
		}

		if ($errors['categorie'] || $errors['libelle'] || $errors['unite'] || $errors['prix_unitaire']) {
			Flight::render('bngrc/articles/form', [
				'mode' => 'edit',
				'article' => [
					'id' => $id,
					'categorie' => $categorie,
					'libelle' => $libelle,
					'unite' => $unite,
					'prix_unitaire' => $prix,
					'actif' => $actif === '1' ? 1 : 0,
				],
				'categories' => $this->categorieOptions(),
				'errors' => $errors,
			]);
			return;
		}

		try {
			$model = new ArticleModel(Flight::db());
			$model->update($id, $categorie, $libelle, $unite, (float)$prix, $actif === '1');
			Flight::redirect('/articles?flash=updated');
		} catch (PDOException $e) {
			$errors['libelle'] = 'Impossible de modifier.';
			Flight::render('bngrc/articles/form', [
				'mode' => 'edit',
				'article' => [
					'id' => $id,
					'categorie' => $categorie,
					'libelle' => $libelle,
					'unite' => $unite,
					'prix_unitaire' => $prix,
					'actif' => $actif === '1' ? 1 : 0,
				],
				'categories' => $this->categorieOptions(),
				'errors' => $errors,
			]);
		}
	}

	public function deactivatePost(int $id): void
	{
		$model = new ArticleModel(Flight::db());
		$model->deactivate($id);
		Flight::redirect('/articles?flash=deactivated');
	}
}
