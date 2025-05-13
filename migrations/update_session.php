<?php
// Запустите этот скрипт, чтобы обновить вашу сессию после изменения роли в базе данных

session_start();
if (isset($_SESSION['user_id'])) {
    // Подключение к базе данных
    $host = 'mysql';
    $db = 'phone_store';
    $user = 'phone_store';
    $password = 'phone_store_password';
    $charset = 'utf8mb4';
    
    try {
        // Подключение к базе
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, $user, $password, $options);
        
        // Получаем актуальную информацию о пользователе из базы
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Обновляем сессию актуальными данными
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            echo "Сессия успешно обновлена!\n";
            echo "ID пользователя: " . $_SESSION['user_id'] . "\n";
            echo "Email пользователя: " . $_SESSION['user_email'] . "\n";
            echo "Роль пользователя: " . $_SESSION['user_role'] . "\n";
        } else {
            echo "Пользователь не найден в базе данных!\n";
        }
    } catch (PDOException $e) {
        echo "Ошибка базы данных: " . $e->getMessage() . "\n";
    }
} else {
    echo "Вы не авторизованы. Сначала войдите в систему.\n";
} 