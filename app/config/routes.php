<?php

use app\controllers\ApiExampleController;
use app\controllers\ProductController;
use app\controllers\WelcomeController;
use app\controllers\bngrc\ArticleController as BngrcArticleController;
use app\controllers\bngrc\BesoinController as BngrcBesoinController;
use app\controllers\bngrc\DashboardController as BngrcDashboardController;
use app\controllers\bngrc\AchatController as BngrcAchatController;
use app\controllers\bngrc\DispatchController as BngrcDispatchController;
use app\controllers\bngrc\RecapController as BngrcRecapController;
use app\controllers\bngrc\SettingsController as BngrcSettingsController;
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

$Welcome_Controller = new WelcomeController();

// Landing page -> go to the real (dynamic) BNGRC dashboard
$router->get('/', function() {
	Flight::redirect('/bngrc/dashboard');
});
$bngrcDashboard = new BngrcDashboardController();
$bngrcRegions = new BngrcRegionController();
$bngrcVilles = new BngrcVilleController();
$bngrcArticles = new BngrcArticleController();
$bngrcBesoins = new BngrcBesoinController();
$bngrcDons = new BngrcDonController();
$bngrcAchats = new BngrcAchatController();
$bngrcDispatch = new BngrcDispatchController();
$bngrcRecap = new BngrcRecapController();
$bngrcSettings = new BngrcSettingsController();

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

// Achats via dons en argent
$router->get('/achats', [$bngrcAchats, 'index']);
$router->get('/achats/add', [$bngrcAchats, 'addForm']);
$router->post('/achats/add', [$bngrcAchats, 'addPost']);

// Dispatch simulation
$router->get('/dispatch', [$bngrcDispatch, 'index']);
$router->post('/dispatch/run', [$bngrcDispatch, 'run']);
$router->post('/dispatch/reset', [$bngrcDispatch, 'reset']);
$router->get('/dispatch/runs', [$bngrcDispatch, 'runs']);
$router->get('/dispatch/runs/@id:[0-9]+', [$bngrcDispatch, 'showRun']);
$router->post('/dispatch/runs/@id:[0-9]+/delete', [$bngrcDispatch, 'deleteRun']);

// Recap
$router->get('/recap', [$bngrcRecap, 'index']);
$router->get('/recap/data', [$bngrcRecap, 'data']);

// Paramètres
$router->get('/settings', [$bngrcSettings, 'index']);
$router->post('/settings', [$bngrcSettings, 'savePost']);

// Ville dashboard détail
$router->get('/villes/@id:[0-9]+/dashboard', [$bngrcVilles, 'dashboard']);

$router->get('/message', [$Welcome_Controller, 'messages']);
$router->get('/inscription', function() { Flight::redirect('/register'); });
$router->get('/logout', [$Welcome_Controller, 'logout']);

}, [ SecurityHeadersMiddleware::class ]);