<?php

use app\controllers\ApiExampleController;
use app\controllers\ProductController;
use app\controllers\WelcomeController;
use app\controllers\bngrc\ArticleController as BngrcArticleController;
use app\controllers\bngrc\BesoinController as BngrcBesoinController;
use app\controllers\bngrc\DashboardController as BngrcDashboardController;
use app\controllers\bngrc\DonController as BngrcDonController;
use app\controllers\bngrc\RegionController as BngrcRegionController;
use app\controllers\bngrc\VilleController as BngrcVilleController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function(Router $router) use ($app) {

// 	$router->get('/', function() use ($app) {
//     $controller = new ApiExampleController($app);
//     $products = $controller->getProduct();

//     $app->render('accueil', ['products' => $products]);
// });

// $router->get('/produit/@id', function($id) use ($app) {
//     $controller = new ProductController($app);
//     $product = $controller->getProductById($id);
    
//     if(!$product) {
//         $app->notFound();
//         return;
//     }   
//     $app->render('produit', ['product' => $product]);
// });

$Welcome_Controller = new WelcomeController();

// Landing page -> go to the real (dynamic) BNGRC dashboard
$router->get('/', function() {
	Flight::redirect('/bngrc/dashboard');
});

// BNGRC dashboard page based on the provided HTML template (served as-is from app/views)
$router->get('/dashboard', function() {
	Flight::render('tableau-bord-bngrc');
});

// BNGRC MVC routes (todolist.yml)
$bngrcDashboard = new BngrcDashboardController();
$bngrcRegions = new BngrcRegionController();
$bngrcVilles = new BngrcVilleController();
$bngrcArticles = new BngrcArticleController();
$bngrcBesoins = new BngrcBesoinController();
$bngrcDons = new BngrcDonController();

$router->get('/bngrc/dashboard', [$bngrcDashboard, 'index']);

$router->get('/regions', [$bngrcRegions, 'index']);
$router->get('/regions/add', [$bngrcRegions, 'addForm']);
$router->post('/regions/add', [$bngrcRegions, 'addPost']);
$router->get('/regions/@id:[0-9]+/edit', [$bngrcRegions, 'editForm']);
$router->post('/regions/@id:[0-9]+/edit', [$bngrcRegions, 'editPost']);
$router->post('/regions/@id:[0-9]+/delete', [$bngrcRegions, 'deletePost']);

$router->get('/villes', [$bngrcVilles, 'index']);
$router->get('/villes/add', [$bngrcVilles, 'addForm']);
$router->post('/villes/add', [$bngrcVilles, 'addPost']);
$router->get('/villes/@id:[0-9]+/edit', [$bngrcVilles, 'editForm']);
$router->post('/villes/@id:[0-9]+/edit', [$bngrcVilles, 'editPost']);
$router->post('/villes/@id:[0-9]+/delete', [$bngrcVilles, 'deletePost']);

$router->get('/articles', [$bngrcArticles, 'index']);
$router->get('/articles/add', [$bngrcArticles, 'addForm']);
$router->post('/articles/add', [$bngrcArticles, 'addPost']);
$router->get('/articles/@id:[0-9]+/edit', [$bngrcArticles, 'editForm']);
$router->post('/articles/@id:[0-9]+/edit', [$bngrcArticles, 'editPost']);
$router->post('/articles/@id:[0-9]+/deactivate', [$bngrcArticles, 'deactivatePost']);

$router->get('/besoins', [$bngrcBesoins, 'index']);
$router->get('/besoins/add', [$bngrcBesoins, 'addForm']);
$router->post('/besoins/add', [$bngrcBesoins, 'addPost']);
$router->get('/besoins/@id:[0-9]+', [$bngrcBesoins, 'show']);
$router->get('/besoins/@id:[0-9]+/edit', [$bngrcBesoins, 'editForm']);
$router->post('/besoins/@id:[0-9]+/edit', [$bngrcBesoins, 'editPost']);
$router->post('/besoins/@id:[0-9]+/delete', [$bngrcBesoins, 'deletePost']);

$router->get('/dons', [$bngrcDons, 'index']);
$router->get('/dons/add', [$bngrcDons, 'addForm']);
$router->post('/dons/add', [$bngrcDons, 'addPost']);
$router->get('/dons/@id:[0-9]+', [$bngrcDons, 'show']);
$router->get('/dons/@id:[0-9]+/edit', [$bngrcDons, 'editForm']);
$router->post('/dons/@id:[0-9]+/edit', [$bngrcDons, 'editPost']);
$router->post('/dons/@id:[0-9]+/delete', [$bngrcDons, 'deletePost']);
// $router->get('/produit/@id', [$Welcome_Controller, 'homeById']);
 $router->get('/message', [$Welcome_Controller,'messages']);
 $router->get('/inscription', function() { Flight::redirect('/register'); });
 $router->get('/logout', [$Welcome_Controller,'logout']);

 $router->get('/register', ['AuthController', 'showRegister']);
 $router->post('/register', ['AuthController', 'postRegister']);
 $router->post('/login', ['AuthController', 'postLogin']);
 $router->post('/api/validate/register', ['AuthController', 'validateRegisterAjax']);

 $router->post('/api/messages/send', ['MessageApiController', 'send']);
 $router->get('/api/messages/conversations', ['MessageApiController', 'conversations']);
 $router->get('/api/messages/@conversationId', ['MessageApiController', 'list']);

 $router->get('/api/users', ['UserApiController', 'list']);

// Serve static-like view HTML files from app/views (e.g. /forms.html -> app/views/forms.html)
$router->get('/@name.html', function($name) use ($app) {
	$path = __DIR__ . $app->get('ds') . '..' . $app->get('ds') . 'views' . $app->get('ds') . $name . '.html';
	// Fallback if $app->get('ds') isn't set
	if (!file_exists($path)) {
		$path = __DIR__ . '/../views/' . $name . '.html';
	}
	if (file_exists($path)) {
		header('Content-Type: text/html; charset=utf-8');
		echo file_get_contents($path);
		return;
	}
	$app->notFound();
});
//  $router->get('/nouv', [$Welcome_Controller, 'pageCreate']);
//  $router->get('/benef', [$Welcome_Controller, 'benefice']);
// // $router->post('/modif/change/@id', [$Welcome_Controller, 'update']);
//  $router->get('/suppr/@id', [$Welcome_Controller, 'annuler']);

//  $router->get('/valide/@id', [$Welcome_Controller, 'validate']);


	// $router->get('/hello-world/@name', function($name) {
	// 	echo '<h1>Hello world! Oh hey '.$name.'!</h1>';
	// });

	// $router->group('/api', function() use ($router) {
	// 	$router->get('/users', [ ApiExampleController::class, 'getUsers' ]);
	// 	$router->get('/users/@id:[0-9]', [ ApiExampleController::class, 'getUser' ]);
	// 	$router->post('/users/@id:[0-9]', [ ApiExampleController::class, 'updateUser' ]);
	// });

	
}, [ SecurityHeadersMiddleware::class ]);