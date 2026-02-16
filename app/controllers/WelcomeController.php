<?php 

namespace app\controllers;
use flight;
use app\models\ProductModel;

class WelcomeController {
// public function home() {
// 	$productModel = new ProductModel(Flight::db());
// 	$produits = $productModel->getLivraison(); // tableau de plusieurs produits
// $data = [];
// foreach ($produits as $produit) {
//     $data[] = [
// 		'id'=> $produit["id"],
//         'daty' => $produit['date_livraison'],
//         'statut' => $produit['statut'],
//         'colis' => $produit['colis'],
//         'chauffeur' => $produit['chauffeur'],
//         'immatriculation' => $produit['immatriculation']
//     ];
// }
// Flight::render('accueil', ['products' => $data]);

// }
// public function homeById($id) {
// 	$productModel = new ProductModel(Flight::db());
// 	$produit = $productModel->getProduitById($id);
// 	$data = ['id' => $produit["id"] ,'name' => $produit["nom"], 'prix' => $produit["prix"], 'url_img' => $produit["url_img"]];
// 	Flight::render('produit',['product' => $data]);
// }

//  public function create() {

//      $data = Flight::request()->data;


//      $daty = $data['daty'];
//      $colis = $data['colis'];
//      $statut = $data['statut'];
//         $chauffeur = $data['chauffeur'];
//         $vehicule = $data['vehicule'];
//         $entrepot = $data['entrepot'];
//         $adresse_destination = $data['adresse_destination'];
//         $cout_revient = $data['cout_revient'];
// //     $file = $files['url_img'];

// //     // Vérifier s'il y a un fichier
// //     if ($file && $file['error'] === UPLOAD_ERR_OK) {

// //         $filename = $file['name'];
// //         $url_img = $filename;
// //     } else {
// //         $url_img = null;
// //     }

//      // Enregistrement en base
//      $productModel = new ProductModel(Flight::db());
//      $productModel->insertLivraison($daty, $statut, $colis, $chauffeur, $vehicule, $entrepot, $adresse_destination, $cout_revient);

//      Flight::redirect('/');
//  }

// public function update($id) {

//     $data = Flight::request()->data;
//     $files = Flight::request()->files;

//     $nom = $data['nom'];
//     $prix = $data['prix'];
//     $file = $files['url_img'];

//     // Vérifier s'il y a un fichier
//     if ($file && $file['error'] === UPLOAD_ERR_OK) {

//         $filename = $file['name'];
//         $url_img = $filename;
//     } else {
//         $url_img = null;
//     }

//     // Enregistrement en base
//     $productModel = new ProductModel(Flight::db());
// 	$productModel->updateProduit($id, $nom, $prix, $url_img);

//     Flight::redirect('/');
// } 
//  public function pageCreate() {
//     $productModel = new ProductModel(Flight::db());
//     $status = $productModel->getStatuts();
//     $colis = $productModel->getColis();
//     $chauffeurs = $productModel->getChauffeurs();
//     $vehicules = $productModel->getVehicules();
//     $entrepots = $productModel->getEntrepots();
//      Flight::render('newProduct', ['status' => $status, 'colis' => $colis, 'chauffeurs' => $chauffeurs, 'vehicules' => $vehicules, 'entrepots' => $entrepots]);

//   }
//  public function benefice() {
//          $productModel = new ProductModel(Flight::db());
//          $produits = $productModel->getLivraison(); // tableau de plusieurs produits
// $products = [];
// foreach ($produits as $produit) {
//     $products[] = [
// 		'id'=> $produit["id"],
//         'daty' => $produit['date_livraison'],
//         'statut' => $produit['statut'],
//         'colis' => $produit['colis'],
//         'chauffeur' => $produit['chauffeur'],
//         'immatriculation' => $produit['immatriculation']
//     ];
// }       
//          $dat = Flight::request()->query;
        
//          $moyen = $dat['benef'];
//          if ($moyen == 1) {
//              $benDay = $productModel->beneficeByDay();
//          } elseif ($moyen == 2) {
//              $benMonth = $productModel->beneficeByMonth();
//          } elseif ($moyen == 3) {
//              $benYear = $productModel->beneficeByYear();
//          } else {
//              $ben = [];
//          }

//      Flight::render('accueil', ['products' => $products, 'benDay' => $benDay ?? [], 'benMonth' => $benMonth ?? [], 'benYear' => $benYear ?? []]);
         
            
//  }
//  public function annuler($id) {
//  	$productModel = new ProductModel(Flight::db());
//      $productModel->annulerLivraison($oid);
//      Flight::redirect('/');
//  }
 public function home(){
    Flight::render('index');
 }
 public function messages(){
    Flight::render('messages');
 }
 public function inscription(){
    Flight::render('inscription');
 }
 
//  public function validate($id) {
//  	$productModel = new ProductModel(Flight::db());
//      $productModel->validateLivraison($id);
//      Flight::redirect('/');
//  }
}
?>