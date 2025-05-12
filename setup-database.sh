#!/bin/bash

# Create sample products table and data
docker exec phone-store-mysql-1 mysql -u phone_store -pphone_store_password phone_store -e "
    -- Create tables if they don't exist
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
    
    -- Create users table if it doesn't exist
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );
    
    -- Create carts table if it doesn't exist
    CREATE TABLE IF NOT EXISTS carts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        session_id VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );
    
    -- Create cart_items table if it doesn't exist
    CREATE TABLE IF NOT EXISTS cart_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cart_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    );
    
    -- Create orders table if it doesn't exist
    CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        total DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
        shipping_address VARCHAR(255) NOT NULL,
        shipping_city VARCHAR(100) NOT NULL,
        shipping_country VARCHAR(100) NOT NULL,
        shipping_zip VARCHAR(20) NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );
    
    -- Create order_items table if it doesn't exist
    CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    );
    
    -- Check if products table is empty
    SET @count = (SELECT COUNT(*) FROM products);
    
    -- Only insert sample data if the table is empty
    SET @sql = IF(@count = 0, 'INSERT INTO products (name, brand, description, price, image, screen_size, storage, ram, os, camera, battery, featured, stock)
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
        (\"iPhone SE\", \"Apple\", \"Affordable iPhone\", 399.99, \"iphonese.jpg\", 4.7, 64, 3, \"iOS\", \"12MP\", \"1821mAh\", 1, 55);', 'SELECT \"Products table already has data, skipping inserts\" as message;');
    
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
"

echo "Database setup completed successfully." 