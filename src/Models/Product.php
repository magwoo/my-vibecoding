<?php

namespace App\Models;

use App\Core\Database;

class Product
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function find($id)
    {
        return $this->db->find("products", $id);
    }
    
    public function getProducts($filters = [], $sort = "created_at DESC", $page = 1, $perPage = 12)
    {
        $sql = "SELECT * FROM products WHERE 1=1";
        $params = [];
        
        // Apply filters
        if (!empty($filters["brand"])) {
            $sql .= " AND brand = ?";
            $params[] = $filters["brand"];
        }
        
        if (!empty($filters["min_price"])) {
            $sql .= " AND price >= ?";
            $params[] = $filters["min_price"];
        }
        
        if (!empty($filters["max_price"])) {
            $sql .= " AND price <= ?";
            $params[] = $filters["max_price"];
        }
        
        if (!empty($filters["screen_size"])) {
            $sql .= " AND screen_size = ?";
            $params[] = $filters["screen_size"];
        }
        
        if (!empty($filters["storage"])) {
            $sql .= " AND storage = ?";
            $params[] = $filters["storage"];
        }
        
        if (!empty($filters["os"])) {
            $sql .= " AND os = ?";
            $params[] = $filters["os"];
        }
        
        if (!empty($filters["search"])) {
            $sql .= " AND (name LIKE ? OR description LIKE ? OR brand LIKE ?)";
            $searchTerm = "%" . $filters["search"] . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Add sorting
        $sql .= " ORDER BY " . $sort;
        
        // Add pagination
        $offset = ($page - 1) * $perPage;
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        return $this->db->query($sql, $params)->fetchAll();
    }
    
    public function getProductCount($filters = [])
    {
        $sql = "SELECT COUNT(*) as count FROM products WHERE 1=1";
        $params = [];
        
        // Apply filters
        if (!empty($filters["brand"])) {
            $sql .= " AND brand = ?";
            $params[] = $filters["brand"];
        }
        
        if (!empty($filters["min_price"])) {
            $sql .= " AND price >= ?";
            $params[] = $filters["min_price"];
        }
        
        if (!empty($filters["max_price"])) {
            $sql .= " AND price <= ?";
            $params[] = $filters["max_price"];
        }
        
        if (!empty($filters["screen_size"])) {
            $sql .= " AND screen_size = ?";
            $params[] = $filters["screen_size"];
        }
        
        if (!empty($filters["storage"])) {
            $sql .= " AND storage = ?";
            $params[] = $filters["storage"];
        }
        
        if (!empty($filters["os"])) {
            $sql .= " AND os = ?";
            $params[] = $filters["os"];
        }
        
        if (!empty($filters["search"])) {
            $sql .= " AND (name LIKE ? OR description LIKE ? OR brand LIKE ?)";
            $searchTerm = "%" . $filters["search"] . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $result = $this->db->query($sql, $params)->fetch();
        return $result ? (int)$result["count"] : 0;
    }
    
    public function getAllBrands()
    {
        $sql = "SELECT DISTINCT brand FROM products ORDER BY brand";
        $result = $this->db->query($sql)->fetchAll();
        return array_column($result, "brand");
    }
    
    public function getAllScreenSizes()
    {
        $sql = "SELECT DISTINCT screen_size FROM products ORDER BY screen_size";
        $result = $this->db->query($sql)->fetchAll();
        return array_column($result, "screen_size");
    }
    
    public function getAllStorageOptions()
    {
        $sql = "SELECT DISTINCT storage FROM products ORDER BY storage";
        $result = $this->db->query($sql)->fetchAll();
        return array_column($result, "storage");
    }
    
    public function getAllOperatingSystems()
    {
        $sql = "SELECT DISTINCT os FROM products ORDER BY os";
        $result = $this->db->query($sql)->fetchAll();
        return array_column($result, "os");
    }
    
    public function getFeaturedProducts($limit = 8)
    {
        $sql = "SELECT * FROM products WHERE featured = 1 ORDER BY created_at DESC LIMIT ?";
        return $this->db->query($sql, [$limit])->fetchAll();
    }
    
    public function create($data)
    {
        return $this->db->insert("products", $data);
    }
    
    public function update($id, $data)
    {
        return $this->db->update("products", $id, $data);
    }
    
    public function delete($id)
    {
        return $this->db->delete("products", $id);
    }
}
