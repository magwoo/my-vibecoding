<?php

namespace Controllers;

use Models\Product;

class HomeController extends Controller {
    public function index() {
        // Получаем несколько новых товаров для отображения на главной странице
        $productModel = new Product();
        $featuredProducts = $productModel->getAll(6, 0, [], 'newest');
        
        $this->render('home/index', [
            'title' => 'Интернет-магазин телефонов',
            'featuredProducts' => $featuredProducts
        ]);
    }
}
