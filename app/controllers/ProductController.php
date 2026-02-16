<?php

namespace app\controllers;

use flight\Engine;

class ProductController {
    protected Engine $app;

    public function __construct(Engine $app) {
        $this->app = $app;
    }
	public function getProduct() {
		$products = [
    [
        'id' => 1,
        'name' => 'Produit 1',
        'price' => 160000,
        'image' => '1.jpg'
    ],
    [
        'id' => 2,
        'name' => 'Produit 1',
        'price' => 160000,
        'image' => '1.jpg'
    ],
    [
        'id' => 3,
        'name' => 'Produit 1',
        'price' => 160000,
        'image' => '2.jpg'
    ],
    [
        'id' => 4,
        'name' => 'Produit 1',
        'price' => 160000,
        'image' => '3.jpg'
    ],
    [
        'id' => 5,
        'name' => 'Produit 1',
        'price' => 160000,
        'image' => '1.jpg'
    ],
    [
        'id' => 6,
        'name' => 'Produit 1',
        'price' => 160000,
        'image' => '1.jpg'
    ],
    [
        'id' => 7,
        'name' => 'Produit 1',
        'price' => 160000,
        'image' => '2.jpg'
    ],
    [
        'id' => 8,
        'name' => 'Produit 1',
        'price' => 160000,
        'image' => '3.jpg'
    ],
    [
        'id' => 9,
        'name' => 'Produit 1',
        'price' => 160000,
        'image' => '1.jpg'
    ],
    [
        'id' => 10,
        'name' => 'Produit 1',
        'price' => 160000,
        'image' => '2.jpg'
    ]
];
		return $products;
	}
		public function getProductById($id) {
    $products = $this->getProduct();
    $product_filtered = array_filter($products, function($data) use ($id) {
        return $data['id'] === (int) $id;
    });
    
    if($product_filtered) {
        return array_pop($product_filtered);
    }
    
    return null; // Produit non trouvé
}
}
?>