<?php

// Database connection settings
$host = 'mysql';
$db = 'phone_store';
$user = 'phone_store';
$password = 'phone_store_password';
$charset = 'utf8mb4';

try {
    // Connect to database
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $user, $password, $options);
    
    echo "Connected to database successfully\n";
    
    // Run migrations
    createTables($pdo);
    seedDatabase($pdo);
    
    echo "Migrations completed successfully\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    exit(1);
}

function createTables($pdo) {
    echo "Creating tables...\n";
    
    // Users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
        name VARCHAR(255) NULL,
        phone VARCHAR(50) NULL,
        address TEXT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    echo "- Users table created\n";
    
    // Products table
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        brand VARCHAR(100) NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        description TEXT NOT NULL,
        screen_size DECIMAL(3, 1) NOT NULL,
        storage VARCHAR(50) NOT NULL,
        os VARCHAR(50) NOT NULL,
        color VARCHAR(50) NOT NULL,
        image_url VARCHAR(255) NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    echo "- Products table created\n";
    
    // Carts table
    $pdo->exec("CREATE TABLE IF NOT EXISTS carts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        session_id VARCHAR(255),
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX (session_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    echo "- Carts table created\n";
    
    // Cart items table
    $pdo->exec("CREATE TABLE IF NOT EXISTS cart_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cart_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        UNIQUE KEY (cart_id, product_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    echo "- Cart items table created\n";
    
    // Orders table
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        total DECIMAL(10, 2) NOT NULL,
        status ENUM('pending', 'processing', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    echo "- Orders table created\n";
    
    // Order items table
    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    echo "- Order items table created\n";
}

function seedDatabase($pdo) {
    echo "Seeding database...\n";
    
    // Seed admin user if it doesn't exist
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute(['admin@example.com']);
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        $hashedPassword = password_hash('admin123', PASSWORD_BCRYPT);
        
        $stmt = $pdo->prepare("INSERT INTO users (email, password, role, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute(['admin@example.com', $hashedPassword, 'admin']);
        
        echo "- Admin user created (email: admin@example.com, password: admin123)\n";
    } else {
        echo "- Admin user already exists\n";
    }
    
    // Check if products exist
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $productCount = $stmt->fetchColumn();
    
    if ($productCount == 0) {
        // Sample products
        $products = [
            [
                'name' => 'iPhone 13',
                'brand' => 'Apple',
                'price' => 799.99,
                'description' => 'The iPhone 13 features a 6.1-inch Super Retina XDR display, A15 Bionic chip, and dual 12MP camera system.',
                'screen_size' => 6.1,
                'storage' => '128GB',
                'os' => 'iOS',
                'color' => 'Midnight',
                'image_url' => 'https://i.imgur.com/Fw4Rbr3.jpeg'
            ],
            [
                'name' => 'iPhone 13 Pro',
                'brand' => 'Apple',
                'price' => 999.99,
                'description' => 'The iPhone 13 Pro features a 6.1-inch ProMotion display, A15 Bionic chip, and triple camera system with LiDAR Scanner.',
                'screen_size' => 6.1,
                'storage' => '256GB',
                'os' => 'iOS',
                'color' => 'Sierra Blue',
                'image_url' => 'https://i.imgur.com/5B7NS2r.jpeg'
            ],
            [
                'name' => 'Galaxy S22',
                'brand' => 'Samsung',
                'price' => 799.99,
                'description' => 'The Galaxy S22 features a 6.1-inch Dynamic AMOLED display, Snapdragon 8 Gen 1 processor, and triple rear camera system.',
                'screen_size' => 6.1,
                'storage' => '128GB',
                'os' => 'Android',
                'color' => 'Phantom Black',
                'image_url' => 'https://i.imgur.com/wFzfS9B.jpeg'
            ],
            [
                'name' => 'Galaxy S22 Ultra',
                'brand' => 'Samsung',
                'price' => 1199.99,
                'description' => 'The Galaxy S22 Ultra features a 6.8-inch Dynamic AMOLED display, Snapdragon 8 Gen 1 processor, and quad rear camera system with S Pen support.',
                'screen_size' => 6.8,
                'storage' => '256GB',
                'os' => 'Android',
                'color' => 'Burgundy',
                'image_url' => 'https://i.imgur.com/wgZ2ZaL.jpeg'
            ],
            [
                'name' => 'Pixel 6',
                'brand' => 'Google',
                'price' => 599.99,
                'description' => 'The Pixel 6 features a 6.4-inch OLED display, Google Tensor chip, and dual rear camera system with night mode.',
                'screen_size' => 6.4,
                'storage' => '128GB',
                'os' => 'Android',
                'color' => 'Sorta Seafoam',
                'image_url' => 'https://i.imgur.com/tJtX4Tp.jpeg'
            ],
            [
                'name' => 'Pixel 6 Pro',
                'brand' => 'Google',
                'price' => 899.99,
                'description' => 'The Pixel 6 Pro features a 6.7-inch LTPO OLED display, Google Tensor chip, and triple rear camera system with 4x optical zoom.',
                'screen_size' => 6.7,
                'storage' => '256GB',
                'os' => 'Android',
                'color' => 'Cloudy White',
                'image_url' => 'https://i.imgur.com/TLlsv7F.jpeg'
            ],
            [
                'name' => 'Redmi Note 11',
                'brand' => 'Xiaomi',
                'price' => 199.99,
                'description' => 'The Redmi Note 11 features a 6.43-inch AMOLED display, Snapdragon 680 processor, and quad rear camera system.',
                'screen_size' => 6.43,
                'storage' => '64GB',
                'os' => 'Android',
                'color' => 'Graphite Gray',
                'image_url' => 'https://i.imgur.com/Q1VU5Hf.jpeg'
            ],
            [
                'name' => 'Xiaomi 12',
                'brand' => 'Xiaomi',
                'price' => 749.99,
                'description' => 'The Xiaomi 12 features a 6.28-inch AMOLED display, Snapdragon 8 Gen 1 processor, and triple rear camera system.',
                'screen_size' => 6.28,
                'storage' => '128GB',
                'os' => 'Android',
                'color' => 'Purple',
                'image_url' => 'https://i.imgur.com/xGnKrFs.jpeg'
            ],
            [
                'name' => 'OnePlus 10 Pro',
                'brand' => 'OnePlus',
                'price' => 899.99,
                'description' => 'The OnePlus 10 Pro features a 6.7-inch Fluid AMOLED display, Snapdragon 8 Gen 1 processor, and triple Hasselblad camera system.',
                'screen_size' => 6.7,
                'storage' => '256GB',
                'os' => 'Android',
                'color' => 'Emerald Forest',
                'image_url' => 'https://i.imgur.com/4VbMX0X.jpeg'
            ],
            [
                'name' => 'iPhone SE (2022)',
                'brand' => 'Apple',
                'price' => 429.99,
                'description' => 'The iPhone SE (2022) features a 4.7-inch Retina HD display, A15 Bionic chip, and 12MP rear camera with Deep Fusion.',
                'screen_size' => 4.7,
                'storage' => '64GB',
                'os' => 'iOS',
                'color' => 'Starlight',
                'image_url' => 'https://i.imgur.com/rSwJNOI.jpeg'
            ],
            [
                'name' => 'Galaxy A53',
                'brand' => 'Samsung',
                'price' => 449.99,
                'description' => 'The Galaxy A53 features a 6.5-inch Super AMOLED display, Exynos 1280 processor, and quad rear camera system.',
                'screen_size' => 6.5,
                'storage' => '128GB',
                'os' => 'Android',
                'color' => 'Awesome Blue',
                'image_url' => 'https://i.imgur.com/vz9rWuB.jpeg'
            ],
            [
                'name' => 'Xiaomi Poco F4',
                'brand' => 'Xiaomi',
                'price' => 399.99,
                'description' => 'The Xiaomi Poco F4 features a 6.67-inch AMOLED display, Snapdragon 870 processor, and triple rear camera system.',
                'screen_size' => 6.67,
                'storage' => '128GB',
                'os' => 'Android',
                'color' => 'Night Black',
                'image_url' => 'https://i.imgur.com/XrAGLZE.jpeg'
            ]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO products (name, brand, price, description, screen_size, storage, os, color, image_url, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        
        foreach ($products as $product) {
            $stmt->execute([
                $product['name'],
                $product['brand'],
                $product['price'],
                $product['description'],
                $product['screen_size'],
                $product['storage'],
                $product['os'],
                $product['color'],
                $product['image_url']
            ]);
        }
        
        echo "- " . count($products) . " sample products added\n";
    } else {
        echo "- Products already exist\n";
    }
} 