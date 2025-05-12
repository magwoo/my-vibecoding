#!/bin/bash

# Create temporary directory
mkdir -p tmp

# Create updated Product.php file
cat > tmp/Product.php << 'EOF'
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
EOF

# Copy fixed Product.php to Docker container
docker cp tmp/Product.php phone-store-php-1:/var/www/html/src/Models/

# Make sure the database table exists
docker exec phone-store-php-1 /bin/bash -c "
if ! mysql -h mysql -u phone_store -pphone_store_password phone_store -e 'SHOW TABLES LIKE \"products\"' | grep -q products; then
    mysql -h mysql -u phone_store -pphone_store_password phone_store -e '
        CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            brand VARCHAR(100) NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            image VARCHAR(255),
            screen_size FLOAT,
            storage INT,
            ram INT,
            os VARCHAR(100),
            camera VARCHAR(100),
            battery VARCHAR(100),
            featured BOOLEAN DEFAULT 0,
            stock INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );
        
        -- Insert some sample data
        INSERT INTO products (name, brand, description, price, image, screen_size, storage, ram, os, camera, battery, featured, stock)
        VALUES
            (\"iPhone 13\", \"Apple\", \"The latest iPhone with A15 Bionic chip\", 799.99, \"iphone13.jpg\", 6.1, 128, 4, \"iOS\", \"12MP\", \"3095mAh\", 1, 50),
            (\"Samsung Galaxy S21\", \"Samsung\", \"Flagship Android phone with great camera\", 699.99, \"s21.jpg\", 6.2, 128, 8, \"Android\", \"64MP\", \"4000mAh\", 1, 45),
            (\"Google Pixel 6\", \"Google\", \"Pure Android experience with amazing camera\", 599.99, \"pixel6.jpg\", 6.4, 128, 8, \"Android\", \"50MP\", \"4614mAh\", 1, 30),
            (\"OnePlus 9\", \"OnePlus\", \"Fast and smooth performance\", 649.99, \"oneplus9.jpg\", 6.55, 128, 8, \"Android\", \"48MP\", \"4500mAh\", 0, 25),
            (\"Xiaomi Mi 11\", \"Xiaomi\", \"Powerful performance at affordable price\", 699.99, \"mi11.jpg\", 6.81, 128, 8, \"Android\", \"108MP\", \"4600mAh\", 0, 35),
            (\"iPhone 12\", \"Apple\", \"Previous generation iPhone\", 699.99, \"iphone12.jpg\", 6.1, 64, 4, \"iOS\", \"12MP\", \"2815mAh\", 1, 40),
            (\"Samsung Galaxy Note 20\", \"Samsung\", \"Productivity powerhouse with S Pen\", 899.99, \"note20.jpg\", 6.7, 256, 8, \"Android\", \"64MP\", \"4300mAh\", 1, 20),
            (\"Sony Xperia 5 III\", \"Sony\", \"Compact flagship with great camera\", 899.99, \"xperia5.jpg\", 6.1, 128, 8, \"Android\", \"12MP\", \"4500mAh\", 0, 15),
            (\"Motorola Edge\", \"Motorola\", \"Curved display with clean Android\", 499.99, \"edge.jpg\", 6.7, 128, 6, \"Android\", \"64MP\", \"4500mAh\", 0, 30),
            (\"iPhone SE\", \"Apple\", \"Affordable iPhone\", 399.99, \"iphonese.jpg\", 4.7, 64, 3, \"iOS\", \"12MP\", \"1821mAh\", 1, 55);
    '
fi
"

# Restart PHP container
docker restart phone-store-php-1

# Clean up
rm -rf tmp 

echo "Product model fix applied successfully." 