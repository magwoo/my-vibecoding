<?php
// Скрипт для отладки логина и принудительной установки сессии
session_start();

// Если пользователь уже вошел, показываем информацию о сессии
if (isset($_SESSION['user_id'])) {
    echo "<h2>Вы уже вошли в систему:</h2>";
    echo "<pre>";
    var_dump($_SESSION);
    echo "</pre>";
    
    echo "<form method='post' action=''>";
    echo "<input type='submit' name='logout' value='Выйти из системы' style='padding: 10px; background-color: #f44336; color: white; border: none; cursor: pointer;'>";
    echo "</form>";
    
    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header("Location: /login_debug.php");
        exit;
    }
    
    echo "<a href='/admin' style='padding: 10px; background-color: #4CAF50; color: white; text-decoration: none; display: inline-block; margin-top: 10px;'>Перейти в админку</a>";
    exit;
}

// Обработка формы логина
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = "Необходимо ввести email и пароль";
    } else {
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
                        echo "<p style='color: green;'><small>Подключено к БД: хост=$host, пользователь=$dbUser</small></p>";
                        break 3; // Выходим из всех циклов, если подключились
                    } catch (PDOException $e) {
                        // Продолжаем со следующей комбинацией
                        continue;
                    }
                }
            }
        }
        
        if (!$connected) {
            $error = "Не удалось подключиться к базе данных";
        } else {
            // Проверка пользователя и пароля
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user) {
                $error = "Пользователь с таким email не найден";
            } else {
                // Проверка пароля
                if (password_verify($password, $user['password'])) {
                    // Логин успешный
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    // Обязательно устанавливаем роль admin
                    if ($user['role'] !== 'admin') {
                        $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
                        $stmt->execute([$user['id']]);
                        $_SESSION['user_role'] = 'admin';
                    }
                    
                    // Перенаправление на админку
                    header("Location: /admin");
                    exit;
                } else {
                    $error = "Неверный пароль";
                }
            }
        }
    }
}

// Форма логина
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отладка логина</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .error {
            color: #e53e3e;
            background-color: #fed7d7;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Вход в систему (Отладка)</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
            </div>
            
            <div>
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div>
                <input type="submit" value="Войти">
            </div>
        </form>
    </div>
</body>
</html> 