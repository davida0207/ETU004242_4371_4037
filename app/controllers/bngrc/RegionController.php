<?php

namespace app\controllers\bngrc;

use app\models\RegionModel;
use Flight;
use PDOException;

class RegionController
{
	public function index(): void
	{
		$regions = [];
		$error = null;
		try {
			$model = new RegionModel(Flight::db());
			$regions = $model->listAll();
		} catch (\Throwable $e) {
			$error = $e->getMessage();
		}
		Flight::render('bngrc/regions/index', [
			'regions' => $regions,
			'flash' => Flight::request()->query['flash'] ?? null,
			'error' => $error,
		]);
	}

	public function addForm(): void
	{
		Flight::render('bngrc/regions/form', [
			'mode' => 'add',
			'region' => ['nom' => ''],
			'errors' => ['nom' => ''],
		]);
	}

	public function addPost(): void
	{
		$nom = (string)(Flight::request()->data->nom ?? '');
		$errors = ['nom' => ''];

		if (trim($nom) === '') {
			$errors['nom'] = 'Le nom de la région est obligatoire.';
		}

		if ($errors['nom'] !== '') {
			Flight::render('bngrc/regions/form', [
				'mode' => 'add',
				'region' => ['nom' => $nom],
				'errors' => $errors,
			]);
			return;
		}

		try {
			$model = new RegionModel(Flight::db());
			$model->create($nom);
			Flight::redirect('/regions?flash=created');
		} catch (PDOException $e) {
			$errors['nom'] = 'Impossible de créer la région (nom déjà utilisé ?).';
			Flight::render('bngrc/regions/form', [
				'mode' => 'add',
				'region' => ['nom' => $nom],
				'errors' => $errors,
			]);
		}
	}

	public function editForm(int $id): void
	{
		$model = new RegionModel(Flight::db());
		$region = $model->getById($id);
		if (!$region) {
			Flight::notFound();
			return;
		}

		Flight::render('bngrc/regions/form', [
			'mode' => 'edit',
			'region' => $region,
			'errors' => ['nom' => ''],
		]);
	}

	public function editPost(int $id): void
	{
		$nom = (string)(Flight::request()->data->nom ?? '');
		$errors = ['nom' => ''];

		if (trim($nom) === '') {
			$errors['nom'] = 'Le nom de la région est obligatoire.';
		}

		if ($errors['nom'] !== '') {
			Flight::render('bngrc/regions/form', [
				'mode' => 'edit',
				'region' => ['id' => $id, 'nom' => $nom],
				'errors' => $errors,
			]);
			return;
		}

		try {
			$model = new RegionModel(Flight::db());
			$model->update($id, $nom);
			Flight::redirect('/regions?flash=updated');
		} catch (PDOException $e) {
			$errors['nom'] = 'Impossible de modifier la région (nom déjà utilisé ?).';
			Flight::render('bngrc/regions/form', [
				'mode' => 'edit',
				'region' => ['id' => $id, 'nom' => $nom],
				'errors' => $errors,
			]);
		}
	}

	public function deletePost(int $id): void
	{
		try {
			$model = new RegionModel(Flight::db());
			$model->delete($id);
			Flight::redirect('/regions?flash=deleted');
		} catch (PDOException $e) {
			// Most likely FK restriction because cities exist.
			Flight::redirect('/regions?flash=blocked');
		}
	}
}
