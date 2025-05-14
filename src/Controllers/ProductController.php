<?php

namespace Controllers;

use Models\Product;

class ProductController extends Controller {
    private $productModel;
    
    public function __construct() {
        $this->productModel = new Product();
    }
    
    // Отображение каталога товаров с фильтрацией и сортировкой
    public function catalog() {
        // Получаем параметры из запроса
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 9; // Товаров на странице
        $offset = ($page - 1) * $limit;
        
        // Параметры фильтрации
        $filters = [];
        
        // Поиск по названию и описанию
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }
        
        // Фильтр по цене
        if (isset($_GET['price_min']) && is_numeric($_GET['price_min'])) {
            $filters['price_min'] = (float)$_GET['price_min'];
        }
        
        if (isset($_GET['price_max']) && is_numeric($_GET['price_max'])) {
            $filters['price_max'] = (float)$_GET['price_max'];
        }
        
        // Фильтр по бренду
        if (isset($_GET['brand']) && !empty($_GET['brand'])) {
            $filters['brand'] = $_GET['brand'];
        }
        
        // Фильтры по характеристикам
        if (isset($_GET['ram']) && !empty($_GET['ram'])) {
            $filters['ram'] = $_GET['ram'];
        }
        
        if (isset($_GET['storage']) && !empty($_GET['storage'])) {
            $filters['storage'] = $_GET['storage'];
        }
        
        if (isset($_GET['screen']) && !empty($_GET['screen'])) {
            $filters['screen'] = $_GET['screen'];
        }
        
        // Сортировка
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
        
        // Получаем товары с применением фильтров и сортировки
        $products = $this->productModel->getAll($limit, $offset, $filters, $sort);
        
        // Получаем общее количество товаров с учетом фильтров для пагинации
        $totalProducts = $this->productModel->countAll($filters);
        $totalPages = ceil($totalProducts / $limit);
        
        // Получаем список брендов для фильтрации
        $brands = $this->productModel->getBrands();
        
        // Получаем список уникальных значений характеристик
        $ramValues = $this->productModel->getSpecsValues('ram');
        $storageValues = $this->productModel->getSpecsValues('storage');
        $screenValues = $this->productModel->getSpecsValues('screen');
        
        $this->render('products/catalog', [
            'title' => 'Каталог телефонов',
            'products' => $products,
            'brands' => $brands,
            'ramValues' => $ramValues,
            'storageValues' => $storageValues,
            'screenValues' => $screenValues,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'filters' => $filters,
            'sort' => $sort
        ]);
    }
    
    // Отображение отдельного товара
    public function view($id) {
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            $this->render('errors/404', [
                'title' => 'Товар не найден'
            ]);
            return;
        }
        
        // Декодируем JSON спецификаций
        $product['specs'] = json_decode($product['specs'], true);
        
        $this->render('products/view', [
            'title' => $product['name'],
            'product' => $product
        ]);
    }
}
