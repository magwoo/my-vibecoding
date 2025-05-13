<?php
// Скрипт для принудительной установки роли admin
session_start();

// Стили для страницы
echo '<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Установка роли админа</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #333;
        }
        .success {
            color: #10b981;
            font-weight: bold;
        }
        .error {
            color: #ef4444;
            font-weight: bold;
        }
        .warning {
            color: #f59e0b;
            font-weight: bold;
        }
        pre {
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            margin-right: 10px;
            margin-top: 10px;
            font-size: 16px;
        }
        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }
        .btn-success {
            background-color: #10b981;
            color: white;
        }
        .btn-danger {
            background-color: #ef4444;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">';

// Если пользователь не авторизован, выводим сообщение
if (!isset($_SESSION['user_id'])) {
    echo '<h1>Ошибка доступа</h1>';
    echo '<p class="error">Вы не авторизованы. Сначала войдите в систему.</p>';
    echo '<a href="/login" class="btn btn-primary">Войти в систему</a>';
    echo '<a href="/login_debug.php" class="btn btn-success">Использовать отладочную форму входа</a>';
    echo '</div></body></html>';
    exit;
}

// Вывод текущей сессии
echo '<h1>Принудительная установка прав администратора</h1>';
echo '<h2>Текущая сессия:</h2>';
echo '<pre>';
var_dump($_SESSION);
echo '</pre>';

// Подключение к базе данных - пробуем несколько вариантов
$possibleHosts = ['localhost', 'mysql', '127.0.0.1', 'db'];
$db = 'phone_store';
$possibleUsers = ['root', 'phone_store'];
$possiblePasswords = ['', 'root', 'password', 'phone_store_password'];
$charset = 'utf8mb4';

$connected = false;
$pdo = null;

foreach ($possibleHosts as $host) {
    foreach ($possibleUsers as $dbUser) {
        foreach ($possiblePasswords as $dbPassword) {
            try {
                $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_TIMEOUT => 1, // Короткий таймаут для быстрой проверки
                ];
                
                $pdo = new PDO($dsn, $dbUser, $dbPassword, $options);
                $connected = true;
                echo "<p><small>Подключено к БД: хост=$host, пользователь=$dbUser</small></p>";
                break 3; // Выходим из всех циклов, если подключились
            } catch (PDOException $e) {
                // Продолжаем со следующей комбинацией
                continue;
            }
        }
    }
}

if (!$connected) {
    echo '<p class="error">Не удалось подключиться к базе данных. Невозможно изменить роль пользователя.</p>';
} else {
    // Автоматически выполняем действия для изменения роли
    $userId = $_SESSION['user_id'];
    
    // 1. Проверяем текущую роль пользователя
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user) {
            echo '<p class="error">Пользователь не найден в базе данных!</p>';
        } else {
            echo '<h2>Информация о пользователе в базе данных:</h2>';
            echo '<ul>';
            echo '<li>ID: ' . $user['id'] . '</li>';
            echo '<li>Email: ' . $user['email'] . '</li>';
            echo '<li>Роль: ' . $user['role'] . '</li>';
            echo '</ul>';
            
            // 2. Изменяем роль пользователя на admin
            $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
            $stmt->execute([$userId]);
            
            // 3. Проверяем успешность изменения
            $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $updatedRole = $stmt->fetchColumn();
            
            if ($updatedRole === 'admin') {
                echo '<p class="success">✅ Роль пользователя успешно изменена на "admin" в базе данных!</p>';
                
                // 4. Обновляем сессию
                $_SESSION['user_role'] = 'admin';
                echo '<p class="success">✅ Сессия успешно обновлена!</p>';
                
                // 5. Проверяем обновленную сессию
                echo '<h2>Обновленная сессия:</h2>';
                echo '<pre>';
                var_dump($_SESSION);
                echo '</pre>';
            } else {
                echo '<p class="error">❌ Не удалось обновить роль пользователя в базе данных!</p>';
            }
        }
    } catch (PDOException $e) {
        echo '<p class="error">❌ Ошибка базы данных: ' . $e->getMessage() . '</p>';
    }
}

// Выводим кнопки для навигации
echo '<div>';
echo '<a href="/admin" class="btn btn-primary">Перейти в админку</a>';
echo '<a href="/check_session.php" class="btn btn-success">Проверить сессию</a>';
echo '<form method="post" action="" style="display: inline;">';
echo '<button type="submit" name="logout" class="btn btn-danger">Выйти из системы</button>';
echo '</form>';
echo '</div>';

// Обработка выхода из системы
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: /login");
    exit;
}

echo '</div></body></html>'; 