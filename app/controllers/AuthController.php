<?php
class AuthController {

  public static function showRegister() {
    Flight::render('inscription', [
      'values' => ['nom'=>'','prenom'=>'','email'=>'','telephone'=>''],
      'errors' => ['nom'=>'','prenom'=>'','email'=>'','password'=>'','confirm_password'=>'','telephone'=>''],
      'success' => false
    ]);
  }

  public static function validateRegisterAjax() {
    try {
      if (extension_loaded('pdo_mysql') === false) {
        Flight::json([
          'ok' => false,
          'errors' => ['_global' => "Configuration serveur: l'extension pdo_mysql (php-mysql) est manquante."],
          'values' => []
        ], 500);
        return;
      }

      $pdo  = Flight::db();
      $repo = new UserRepository($pdo);

      $req = Flight::request();

      $input = [
        'nom' => $req->data->nom,
        'prenom' => $req->data->prenom,
        'email' => $req->data->email,
        'password' => $req->data->password,
        'confirm_password' => $req->data->confirm_password,
        'telephone' => $req->data->telephone,
      ];

      $res = Validator::validateRegister($input, $repo);

      Flight::json([
        'ok' => $res['ok'],
        'errors' => $res['errors'],
        'values' => $res['values'],
      ]);
    } catch (Throwable $e) {
      $payload = [
        'ok' => false,
        'errors' => ['_global' => 'Erreur serveur lors de la validation.'],
        'values' => []
      ];

      if (class_exists('Tracy\\Debugger') && \Tracy\Debugger::$showBar === true) {
        $payload['errors']['_debug'] = $e->getMessage();
      }

      Flight::json($payload, 500);
    }
  }

  public static function postRegister() {
    $pdo  = Flight::db();
    $repo = new UserRepository($pdo);
    $svc  = new UserService($repo);

    $req = Flight::request();

    $input = [
      'nom' => $req->data->nom,
      'prenom' => $req->data->prenom,
      'email' => $req->data->email,
      'password' => $req->data->password,
      'confirm_password' => $req->data->confirm_password,
      'telephone' => $req->data->telephone,
    ];

    $res = Validator::validateRegister($input, $repo);

    if ($res['ok']) {
      $userId = $svc->register($res['values'], (string)$input['password']);

      // Auto-login after successful registration
      $_SESSION['user_id'] = $userId;
      $_SESSION['user_nom'] = (string)($res['values']['nom'] ?? '');

    // When the user logs in (auto-login after registration), mark pending incoming messages as delivered.
    try {
      $msgRepo = new MessageRepository(Flight::db());
      $msgRepo->markDeliveredForUser((int)$userId);
    } catch (Throwable $e) {
      // Ignore delivery update errors during auth.
    }

      Flight::redirect('/home');
      return;
    }

    Flight::render('inscription', [
      'values' => $res['values'],
      'errors' => $res['errors'],
      'success' => false
    ]);
  }
  public static function showLogin() {
    Flight::render('login', [
      'values' => ['nom'=>'','email'=>''],
      'errors' => ['nom'=>'','email'=>'','password'=>'','_global'=>''],
    ]);
  }
  public static function postLogin() {
    $pdo  = Flight::db();
    $repo = new UserRepository($pdo);

    $req = Flight::request();

    $email = strtolower(trim((string)($req->data->email ?? '')));
    $nom = trim((string)($req->data->nom ?? ''));
    $password = (string)($req->data->password ?? '');

    $errors = ['nom'=>'','email'=>'','password'=>'','_global'=>''];
    $values = ['nom'=>$nom, 'email'=>$email];

    if ($email === '') {
      $errors['email'] = "L'email est obligatoire.";
    }
    if ($nom === '') {
      $errors['nom'] = "Le nom est obligatoire.";
    }
    if ($password === '') {
      $errors['password'] = "Le mot de passe est obligatoire.";
    }

    if ($errors['nom'] === '' && $errors['email'] === '' && $errors['password'] === '') {
      try {
        $user = $repo->getByEmail($email);

        if ($user) {
          if (!password_verify($password, (string)$user['password_hash'])) {
            $errors['_global'] = "Email ou mot de passe incorrect.";
          } else {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];

			try {
			  $msgRepo = new MessageRepository(Flight::db());
			  $msgRepo->markDeliveredForUser((int)$user['id']);
			} catch (Throwable $e) {
			}
            Flight::redirect('/home');
            return;
          }
        } else {
          // Auto-create only if the email does NOT already exist.
          // This avoids DB errors when a user tries to login with an email already used.
          $newUserId = $repo->autocCreate($nom, $email, password_hash((string)$password, PASSWORD_DEFAULT));
          $_SESSION['user_id'] = $newUserId;
          $_SESSION['user_nom'] = $nom;

		  try {
			  $msgRepo = new MessageRepository(Flight::db());
			  $msgRepo->markDeliveredForUser((int)$newUserId);
		  } catch (Throwable $e) {
		  }
          Flight::redirect('/home');
          return;
        }
      } catch (Throwable $e) {
        $errors['_global'] = "Erreur serveur pendant la connexion.";
      }
    }

    Flight::render('login', [
      'values' => $values,
      'errors' => $errors,
    ]);
  }
  
}
