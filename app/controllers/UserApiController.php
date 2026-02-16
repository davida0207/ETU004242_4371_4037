<?php

class UserApiController
{
	public static function list()
	{
		// Session is started in bootstrap.php, but keep this safe for direct calls.
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}

		if (empty($_SESSION['user_id'])) {
			Flight::json([
				'ok' => false,
				'error' => 'Not authenticated',
			], 401);
			return;
		}

		$currentUserId = (int)$_SESSION['user_id'];

		$pdo = Flight::db();
		$st = $pdo->prepare('SELECT id, nom, prenom, email FROM users WHERE id <> ? ORDER BY nom, prenom');
		$st->execute([$currentUserId]);
		$users = $st->fetchAll(PDO::FETCH_ASSOC);

		Flight::json([
			'ok' => true,
			'users' => $users,
		]);
	}
}
