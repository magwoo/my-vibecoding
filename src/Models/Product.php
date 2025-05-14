<?php

namespace Models;

use Utils\Database;

class Product {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Получить все товары
    public function getAll($limit = null, $offset = 0, $filters = [], $sort = null) {
        $sql = "SELECT * FROM products WHERE 1=1";
        $params = [];
        
        // Применяем фильтры
        if (!empty($filters)) {
            // Фильтр по названию и описанию (поиск)
            if (!empty($filters['search'])) {
                $sql .= " AND (name LIKE ? OR description LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            // Фильтр по цене (мин)
            if (!empty($filters['price_min'])) {
                $sql .= " AND price >= ?";
                $params[] = $filters['price_min'];
            }
            
            // Фильтр по цене (макс)
            if (!empty($filters['price_max'])) {
                $sql .= " AND price <= ?";
                $params[] = $filters['price_max'];
            }
            
            // Фильтр по бренду
            if (!empty($filters['brand'])) {
                $sql .= " AND brand = ?";
                $params[] = $filters['brand'];
            }
            
            // Фильтры по характеристикам
            // ОЗУ
            if (!empty($filters['ram'])) {
                $sql .= " AND JSON_EXTRACT(specs, '$.ram') LIKE ?";
                $params[] = '%' . $filters['ram'] . '%';
            }
            
            // Объем памяти
            if (!empty($filters['storage'])) {
                $sql .= " AND JSON_EXTRACT(specs, '$.storage') LIKE ?";
                $params[] = '%' . $filters['storage'] . '%';
            }
            
            // Диагональ экрана
            if (!empty($filters['screen'])) {
                $sql .= " AND JSON_EXTRACT(specs, '$.screen') LIKE ?";
                $params[] = '%' . $filters['screen'] . '%';
            }
        }
        
        // Применяем сортировку
        if (!empty($sort)) {
            switch ($sort) {
                case 'price_asc':
                    $sql .= " ORDER BY price ASC";
                    break;
                case 'price_desc':
                    $sql .= " ORDER BY price DESC";
                    break;
                case 'newest':
                    $sql .= " ORDER BY created_at DESC";
                    break;
                default:
                    $sql .= " ORDER BY id ASC";
            }
        } else {
            $sql .= " ORDER BY id ASC";
        }
        
        // Применяем ограничение выборки
        if ($limit !== null) {
            $sql .= " LIMIT ?, ?";
            $params[] = (int)$offset;
            $params[] = (int)$limit;
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    // Получить товар по ID
    public function getById($id) {
        $sql = "SELECT * FROM products WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    // Получить список брендов
    public function getBrands() {
        $sql = "SELECT DISTINCT brand FROM products ORDER BY brand";
        $result = $this->db->fetchAll($sql);
        
        // Преобразуем результат в простой массив брендов
        $brands = [];
        foreach ($result as $row) {
            $brands[] = $row['brand'];
        }
        
        return $brands;
    }
    
    // Получить уникальные значения характеристик для фильтров
    public function getSpecsValues($specName) {
        $sql = "SELECT DISTINCT JSON_EXTRACT(specs, '$." . $specName . "') as value FROM products";
        $result = $this->db->fetchAll($sql);
        
        // Преобразуем результат в простой массив значений
        $values = [];
        foreach ($result as $row) {
            // Удаляем кавычки из JSON-значения
            $value = trim($row['value'], '"');
            if (!empty($value)) {
                $values[] = $value;
            }
        }
        
        return $values;
    }
    
    // Подсчитать общее количество товаров с учетом фильтров
    public function countAll($filters = []) {
        $sql = "SELECT COUNT(*) as count FROM products WHERE 1=1";
        $params = [];
        
        // Применяем фильтры
        if (!empty($filters)) {
            // Фильтр по названию и описанию (поиск)
            if (!empty($filters['search'])) {
                $sql .= " AND (name LIKE ? OR description LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            // Фильтр по цене (мин)
            if (!empty($filters['price_min'])) {
                $sql .= " AND price >= ?";
                $params[] = $filters['price_min'];
            }
            
            // Фильтр по цене (макс)
            if (!empty($filters['price_max'])) {
                $sql .= " AND price <= ?";
                $params[] = $filters['price_max'];
            }
            
            // Фильтр по бренду
            if (!empty($filters['brand'])) {
                $sql .= " AND brand = ?";
                $params[] = $filters['brand'];
            }
            
            // Фильтры по характеристикам
            // ОЗУ
            if (!empty($filters['ram'])) {
                $sql .= " AND JSON_EXTRACT(specs, '$.ram') LIKE ?";
                $params[] = '%' . $filters['ram'] . '%';
            }
            
            // Объем памяти
            if (!empty($filters['storage'])) {
                $sql .= " AND JSON_EXTRACT(specs, '$.storage') LIKE ?";
                $params[] = '%' . $filters['storage'] . '%';
            }
            
            // Диагональ экрана
            if (!empty($filters['screen'])) {
                $sql .= " AND JSON_EXTRACT(specs, '$.screen') LIKE ?";
                $params[] = '%' . $filters['screen'] . '%';
            }
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'];
    }
    
    // Создать новый товар
    public function create($data) {
        $sql = "INSERT INTO products (name, description, price, brand, specs, image_path) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['name'],
            $data['description'],
            $data['price'],
            $data['brand'],
            json_encode($data['specs']),
            $data['image_path']
        ];
        
        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }
    
    // Обновить товар
    public function update($id, $data) {
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, 
                brand = ?, specs = ?, image_path = ? WHERE id = ?";
        
        $params = [
            $data['name'],
            $data['description'],
            $data['price'],
            $data['brand'],
            json_encode($data['specs']),
            $data['image_path'],
            $id
        ];
        
        return $this->db->query($sql, $params)->rowCount() > 0;
    }
    
    // Удалить товар
    public function delete($id) {
        $sql = "DELETE FROM products WHERE id = ?";
        return $this->db->query($sql, [$id])->rowCount() > 0;
    }
}
