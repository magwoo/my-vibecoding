<?php

namespace Controllers\Admin;

use Controllers\Controller;
use Models\Product;

class ProductController extends Controller {
    private $productModel;
    
    public function __construct() {
        $this->productModel = new Product();
    }
    
    // Список всех товаров
    public function index() {
        // Проверяем авторизацию администратора
        $this->requireAdmin();
        
        // Параметры пагинации
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10; // Товаров на странице
        $offset = ($page - 1) * $limit;
        
        // Параметры поиска и фильтрации
        $filters = [];
        
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }
        
        if (isset($_GET['brand']) && !empty($_GET['brand'])) {
            $filters['brand'] = $_GET['brand'];
        }
        
        // Получаем товары с учетом пагинации и фильтров
        $products = $this->productModel->getAll($limit, $offset, $filters);
        
        // Получаем общее количество товаров для пагинации
        $totalProducts = $this->productModel->countAll($filters);
        $totalPages = ceil($totalProducts / $limit);
        
        // Получаем список брендов для фильтрации
        $brands = $this->productModel->getBrands();
        
        $this->renderAdmin('products/index', [
            'title' => 'Управление товарами',
            'products' => $products,
            'brands' => $brands,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'filters' => $filters
        ]);
    }
    
    // Форма создания нового товара
    public function create() {
        // Проверяем авторизацию администратора
        $this->requireAdmin();
        
        // Получаем список брендов для выпадающего списка
        $brands = $this->productModel->getBrands();
        
        $this->renderAdmin('products/create', [
            'title' => 'Добавление нового товара',
            'brands' => $brands
        ]);
    }
    
    // Обработка создания нового товара
    public function store() {
        // Проверяем авторизацию администратора
        $this->requireAdmin();
        
        // Проверка CSRF-токена
        $this->validateCsrfToken();
        
        // Получаем данные из формы
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
        $brand = isset($_POST['brand']) ? trim($_POST['brand']) : '';
        
        // Получаем характеристики
        $specs = [
            'ram' => isset($_POST['ram']) ? trim($_POST['ram']) : '',
            'storage' => isset($_POST['storage']) ? trim($_POST['storage']) : '',
            'screen' => isset($_POST['screen']) ? trim($_POST['screen']) : '',
            'processor' => isset($_POST['processor']) ? trim($_POST['processor']) : '',
            'camera' => isset($_POST['camera']) ? trim($_POST['camera']) : ''
        ];
        
        // Проверка обязательных полей
        if (empty($name) || empty($brand) || $price <= 0) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Название, бренд и цена обязательны для заполнения'
            ];
            $this->redirect('/admin/products/create');
        }
        
        // Обработка загрузки изображения
        $imagePath = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadsDir = ROOT_PATH . '/uploads/';
            
            // Создаем директорию, если не существует
            if (!file_exists($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }
            
            // Генерируем уникальное имя файла
            $fileName = uniqid() . '_' . $_FILES['image']['name'];
            $filePath = $uploadsDir . $fileName;
            
            // Перемещаем загруженный файл
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                $imagePath = '/uploads/' . $fileName;
            } else {
                $_SESSION['flash_message'] = [
                    'type' => 'error',
                    'message' => 'Ошибка при загрузке изображения'
                ];
                $this->redirect('/admin/products/create');
            }
        }
        
        // Создаем новый товар
        $productData = [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'brand' => $brand,
            'specs' => $specs,
            'image_path' => $imagePath
        ];
        
        $productId = $this->productModel->create($productData);
        
        if ($productId) {
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Товар успешно добавлен'
            ];
            $this->redirect('/admin/products');
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Ошибка при добавлении товара'
            ];
            $this->redirect('/admin/products/create');
        }
    }
    
    // Форма редактирования товара
    public function edit($id) {
        // Проверяем авторизацию администратора
        $this->requireAdmin();
        
        // Получаем данные товара
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            $this->renderAdmin('errors/404', [
                'title' => 'Товар не найден'
            ]);
            return;
        }
        
        // Декодируем JSON спецификаций
        $product['specs'] = json_decode($product['specs'], true);
        
        // Получаем список брендов для выпадающего списка
        $brands = $this->productModel->getBrands();
        
        $this->renderAdmin('products/edit', [
            'title' => 'Редактирование товара',
            'product' => $product,
            'brands' => $brands
        ]);
    }
    
    // Обработка обновления товара
    public function update($id) {
        // Проверяем авторизацию администратора
        $this->requireAdmin();
        
        // Проверка CSRF-токена
        $this->validateCsrfToken();
        
        // Получаем данные товара
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            $this->renderAdmin('errors/404', [
                'title' => 'Товар не найден'
            ]);
            return;
        }
        
        // Получаем данные из формы
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
        $brand = isset($_POST['brand']) ? trim($_POST['brand']) : '';
        
        // Получаем характеристики
        $specs = [
            'ram' => isset($_POST['ram']) ? trim($_POST['ram']) : '',
            'storage' => isset($_POST['storage']) ? trim($_POST['storage']) : '',
            'screen' => isset($_POST['screen']) ? trim($_POST['screen']) : '',
            'processor' => isset($_POST['processor']) ? trim($_POST['processor']) : '',
            'camera' => isset($_POST['camera']) ? trim($_POST['camera']) : ''
        ];
        
        // Проверка обязательных полей
        if (empty($name) || empty($brand) || $price <= 0) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Название, бренд и цена обязательны для заполнения'
            ];
            $this->redirect('/admin/products/edit/' . $id);
        }
        
        // Обработка загрузки изображения
        $imagePath = $product['image_path']; // По умолчанию используем текущее изображение
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadsDir = ROOT_PATH . '/uploads/';
            
            // Создаем директорию, если не существует
            if (!file_exists($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }
            
            // Генерируем уникальное имя файла
            $fileName = uniqid() . '_' . $_FILES['image']['name'];
            $filePath = $uploadsDir . $fileName;
            
            // Перемещаем загруженный файл
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                $imagePath = '/uploads/' . $fileName;
                
                // Удаляем старое изображение, если оно существует
                if (!empty($product['image_path']) && file_exists(ROOT_PATH . $product['image_path'])) {
                    unlink(ROOT_PATH . $product['image_path']);
                }
            } else {
                $_SESSION['flash_message'] = [
                    'type' => 'error',
                    'message' => 'Ошибка при загрузке изображения'
                ];
                $this->redirect('/admin/products/edit/' . $id);
            }
        }
        
        // Обновляем товар
        $productData = [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'brand' => $brand,
            'specs' => $specs,
            'image_path' => $imagePath
        ];
        
        $result = $this->productModel->update($id, $productData);
        
        if ($result) {
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Товар успешно обновлен'
            ];
            $this->redirect('/admin/products');
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Ошибка при обновлении товара'
            ];
            $this->redirect('/admin/products/edit/' . $id);
        }
    }
    
    // Удаление товара
    public function delete($id) {
        // Проверяем авторизацию администратора
        $this->requireAdmin();
        
        // Проверка CSRF-токена
        $this->validateCsrfToken();
        
        // Получаем данные товара
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            $this->json(['success' => false, 'message' => 'Товар не найден'], 404);
        }
        
        // Удаляем изображение товара, если оно существует
        if (!empty($product['image_path']) && file_exists(ROOT_PATH . $product['image_path'])) {
            unlink(ROOT_PATH . $product['image_path']);
        }
        
        // Удаляем товар из базы данных
        $result = $this->productModel->delete($id);
        
        if ($result) {
            $this->json(['success' => true, 'message' => 'Товар успешно удален']);
        } else {
            $this->json(['success' => false, 'message' => 'Ошибка при удалении товара'], 500);
        }
    }
}
