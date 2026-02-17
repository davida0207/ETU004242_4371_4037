<?php

namespace app\controllers\bngrc;

use app\models\SettingModel;
use Flight;

class SettingsController
{
	public function index(): void
	{
		$db = Flight::db();
		$model = new SettingModel($db);

		$fraisPercent = $model->getPurchaseFeePercent(
			(float)(Flight::get('config')['bngrc']['purchase_fee_percent'] ?? 10.0)
		);

		$flash = (string)(Flight::request()->query['flash'] ?? '');

		Flight::render('bngrc/settings/index', [
			'frais_percent' => $fraisPercent,
			'flash' => $flash,
		]);
	}

	public function savePost(): void
	{
		$db = Flight::db();
		$model = new SettingModel($db);
		$req = Flight::request();

		$fraisPercent = (string)($req->data->purchase_fee_percent ?? '');

		$errors = [];

		if (!is_numeric($fraisPercent) || (float)$fraisPercent < 0 || (float)$fraisPercent > 100) {
			$errors['purchase_fee_percent'] = 'Le pourcentage doit être un nombre entre 0 et 100.';
		}

		if (!empty($errors)) {
			Flight::render('bngrc/settings/index', [
				'frais_percent' => $fraisPercent,
				'flash' => '',
				'errors' => $errors,
			]);
			return;
		}

		$model->set('purchase_fee_percent', (string)(float)$fraisPercent, 'Frais d\'achat (%)');

		Flight::redirect('/settings?flash=saved');
	}
}
