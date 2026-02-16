<?php

namespace app\controllers\bngrc;

use app\models\RegionModel;
use app\models\VilleModel;
use Flight;
use PDOException;

class VilleController
{
	public function index(): void
	{
		$regionIdRaw = Flight::request()->query['region_id'] ?? null;
		$regionId = $regionIdRaw !== null && $regionIdRaw !== '' ? (int)$regionIdRaw : null;

		$regionModel = new RegionModel(Flight::db());
		$villeModel = new VilleModel(Flight::db());

		Flight::render('bngrc/villes/index', [
			'villes' => $villeModel->listAll($regionId),
			'regions' => $regionModel->listAll(),
			'filters' => ['region_id' => $regionId],
			'flash' => Flight::request()->query['flash'] ?? null,
		]);
	}

	public function addForm(): void
	{
		$regionModel = new RegionModel(Flight::db());
		Flight::render('bngrc/villes/form', [
			'mode' => 'add',
			'ville' => ['nom' => '', 'region_id' => ''],
			'regions' => $regionModel->listAll(),
			'errors' => ['nom' => '', 'region_id' => ''],
		]);
	}

	public function addPost(): void
	{
		$nom = (string)(Flight::request()->data->nom ?? '');
		$regionId = (string)(Flight::request()->data->region_id ?? '');
		$errors = ['nom' => '', 'region_id' => ''];

		if (trim($nom) === '') {
			$errors['nom'] = 'Le nom de la ville est obligatoire.';
		}
		if ($regionId === '' || (int)$regionId <= 0) {
			$errors['region_id'] = 'La région est obligatoire.';
		}

		$regionModel = new RegionModel(Flight::db());
		if ($errors['nom'] !== '' || $errors['region_id'] !== '') {
			Flight::render('bngrc/villes/form', [
				'mode' => 'add',
				'ville' => ['nom' => $nom, 'region_id' => $regionId],
				'regions' => $regionModel->listAll(),
				'errors' => $errors,
			]);
			return;
		}

		try {
			$model = new VilleModel(Flight::db());
			$model->create((int)$regionId, $nom);
			Flight::redirect('/villes?flash=created');
		} catch (PDOException $e) {
			$errors['nom'] = 'Impossible de créer la ville (déjà existante ?).';
			Flight::render('bngrc/villes/form', [
				'mode' => 'add',
				'ville' => ['nom' => $nom, 'region_id' => $regionId],
				'regions' => $regionModel->listAll(),
				'errors' => $errors,
			]);
		}
	}

	public function editForm(int $id): void
	{
		$regionModel = new RegionModel(Flight::db());
		$villeModel = new VilleModel(Flight::db());
		$ville = $villeModel->getById($id);
		if (!$ville) {
			Flight::notFound();
			return;
		}

		Flight::render('bngrc/villes/form', [
			'mode' => 'edit',
			'ville' => $ville,
			'regions' => $regionModel->listAll(),
			'errors' => ['nom' => '', 'region_id' => ''],
		]);
	}

	public function editPost(int $id): void
	{
		$nom = (string)(Flight::request()->data->nom ?? '');
		$regionId = (string)(Flight::request()->data->region_id ?? '');
		$errors = ['nom' => '', 'region_id' => ''];

		if (trim($nom) === '') {
			$errors['nom'] = 'Le nom de la ville est obligatoire.';
		}
		if ($regionId === '' || (int)$regionId <= 0) {
			$errors['region_id'] = 'La région est obligatoire.';
		}

		$regionModel = new RegionModel(Flight::db());
		if ($errors['nom'] !== '' || $errors['region_id'] !== '') {
			Flight::render('bngrc/villes/form', [
				'mode' => 'edit',
				'ville' => ['id' => $id, 'nom' => $nom, 'region_id' => $regionId],
				'regions' => $regionModel->listAll(),
				'errors' => $errors,
			]);
			return;
		}

		try {
			$model = new VilleModel(Flight::db());
			$model->update($id, (int)$regionId, $nom);
			Flight::redirect('/villes?flash=updated');
		} catch (PDOException $e) {
			$errors['nom'] = 'Impossible de modifier la ville.';
			Flight::render('bngrc/villes/form', [
				'mode' => 'edit',
				'ville' => ['id' => $id, 'nom' => $nom, 'region_id' => $regionId],
				'regions' => $regionModel->listAll(),
				'errors' => $errors,
			]);
		}
	}

	public function deletePost(int $id): void
	{
		try {
			$model = new VilleModel(Flight::db());
			$model->delete($id);
			Flight::redirect('/villes?flash=deleted');
		} catch (PDOException $e) {
			Flight::redirect('/villes?flash=blocked');
		}
	}
}
