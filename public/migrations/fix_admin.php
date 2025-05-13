<?php
// Скрипт для полной настройки проекта
session_start();

// Функция для вывода HTML со стилями
function showPage($content) {
    echo '<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Исправление доступа к админке и профилю</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        h1, h2, h3 {
            color: #333;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
        hr {
            border: 0;
            height: 1px;
            background-color: #ddd;
            margin: 20px 0;
        }
        pre {
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
        ul, ol {
            padding-left: 25px;
        }
        a {
            color: #3b82f6;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .button {
            display: inline-block;
            background-color: #10b981;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 10px;
        }
        .button:hover {
            background-color: #059669;
            text-decoration: none;
        }
        form {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9fafb;
            border-radius: 4px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #10b981;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #059669;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Исправление доступа к админке и профилю</h1>
        ' . $content . '
    </div>
</body>
</html>';
}

$content = '';

// Если это новое подключение к БД
if (isset($_POST['db_connect'])) {
    $host = $_POST['host'];
    $db = $_POST['db'];
    $user = $_POST['user'];
    $password = $_POST['password'];
    $charset = 'utf8mb4';
} else {
    // Попробуем определить настройки автоматически из файла миграций
    $migrationsFilePath = __DIR__ . '/../../migrations/run.php';
    
    if (file_exists($migrationsFilePath)) {
        $migrationsContent = file_get_contents($migrationsFilePath);
        
        // Пытаемся извлечь настройки подключения
        preg_match('/\$host\s*=\s*[\'"](.+?)[\'"]/', $migrationsContent, $hostMatches);
        preg_match('/\$db\s*=\s*[\'"](.+?)[\'"]/', $migrationsContent, $dbMatches);
        preg_match('/\$user\s*=\s*[\'"](.+?)[\'"]/', $migrationsContent, $userMatches);
        preg_match('/\$password\s*=\s*[\'"](.+?)[\'"]/', $migrationsContent, $passwordMatches);
        
        // Если нашли параметры в файле миграций
        if (!empty($hostMatches[1]) && !empty($dbMatches[1]) && !empty($userMatches[1])) {
            $host = $hostMatches[1];
            $db = $dbMatches[1];
            $user = $userMatches[1];
            $password = isset($passwordMatches[1]) ? $passwordMatches[1] : '';
            $charset = 'utf8mb4';
            
            $content .= "<p>Параметры подключения к базе данных автоматически определены из файла миграций.</p>";
        } else {
            // Стандартные параметры
            $host = 'localhost';
            $db = 'phone_store';
            $user = 'root';
            $password = '';
            $charset = 'utf8mb4';
        }
    } else {
        // Стандартные параметры
        $host = 'localhost';
        $db = 'phone_store';
        $user = 'root';
        $password = '';
        $charset = 'utf8mb4';
    }
    
    // Дополнительные варианты хоста для Docker
    $alternativeHosts = ['localhost', 'mysql', '127.0.0.1', 'db'];
}

// Пробуем все возможные хосты, если есть ошибка подключения
$connected = false;
$connectionError = '';

if (!isset($_POST['db_connect'])) {
    // Если это не POST-запрос, попробуем автоматически подключиться с разными хостами
    foreach ($alternativeHosts as $currentHost) {
        try {
            $dsn = "mysql:host=$currentHost;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 3, // Таймаут 3 секунды
            ];
            
            $pdo = new PDO($dsn, $user, $password, $options);
            $connected = true;
            $host = $currentHost; // Запоминаем рабочий хост
            break;
        } catch (PDOException $e) {
            $connectionError = $e->getMessage();
            continue;
        }
    }
} else {
    // Если это POST-запрос, подключаемся с указанными параметрами
    try {
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, $user, $password, $options);
        $connected = true;
    } catch (PDOException $e) {
        $connectionError = $e->getMessage();
    }
}

// Если не удалось подключиться, показываем форму ввода параметров
if (!$connected) {
    $content .= "<h2>Не удалось подключиться к базе данных</h2>";
    $content .= "<p class='error'>Ошибка: " . $connectionError . "</p>";
    $content .= "<p>Введите параметры подключения к базе данных вручную:</p>";
    
    $content .= "<form method='post' action=''>";
    $content .= "<input type='hidden' name='db_connect' value='1'>";
    $content .= "<label for='host'>Хост базы данных:</label>";
    $content .= "<input type='text' id='host' name='host' value='" . htmlspecialchars($host) . "' required>";
    $content .= "<label for='db'>Имя базы данных:</label>";
    $content .= "<input type='text' id='db' name='db' value='" . htmlspecialchars($db) . "' required>";
    $content .= "<label for='user'>Имя пользователя:</label>";
    $content .= "<input type='text' id='user' name='user' value='" . htmlspecialchars($user) . "' required>";
    $content .= "<label for='password'>Пароль:</label>";
    $content .= "<input type='password' id='password' name='password' value='" . htmlspecialchars($password) . "'>";
    $content .= "<input type='submit' value='Подключиться'>";
    $content .= "</form>";
    
    $content .= "<hr>";
    $content .= "<p>Стандартные параметры подключения для разных серверов:</p>";
    $content .= "<ul>";
    $content .= "<li><strong>MAMP/XAMPP:</strong> хост = localhost, пользователь = root, пароль = root (или пустой)</li>";
    $content .= "<li><strong>Docker:</strong> хост = mysql, пользователь = phone_store, пароль = phone_store_password</li>";
    $content .= "</ul>";
    
    // Выводим страницу и завершаем скрипт
    showPage($content);
    exit;
}

// Если подключились, выполняем основную логику скрипта
$content .= "<h2>Подключение к базе данных выполнено успешно!</h2>";
$content .= "<p>Используемые параметры: хост = <strong>{$host}</strong>, база = <strong>{$db}</strong>, пользователь = <strong>{$user}</strong></p>";

// 1. Проверяем и добавляем столбцы name, phone, address в таблицу users, если их нет
$columns = [];
$result = $pdo->query("SHOW COLUMNS FROM users");
while ($row = $result->fetch()) {
    $columns[] = $row['Field'];
}

$columnsToAdd = [];
if (!in_array('name', $columns)) {
    $columnsToAdd[] = "ADD COLUMN name VARCHAR(255) NULL AFTER role";
}
if (!in_array('phone', $columns)) {
    $columnsToAdd[] = "ADD COLUMN phone VARCHAR(50) NULL AFTER name";
}
if (!in_array('address', $columns)) {
    $columnsToAdd[] = "ADD COLUMN address TEXT NULL AFTER phone";
}

if (!empty($columnsToAdd)) {
    $sql = "ALTER TABLE users " . implode(", ", $columnsToAdd);
    $pdo->exec($sql);
    $content .= "<p class='success'>✅ Добавлены необходимые поля в таблицу пользователей.</p>";
} else {
    $content .= "<p class='success'>✅ Поля в таблице пользователей уже существуют.</p>";
}

// 2. Если пользователь авторизован, обновляем его роль на admin
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    // Устанавливаем роль admin для текущего пользователя
    $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
    $stmt->execute([$userId]);
    
    // Получаем актуальную информацию о пользователе
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Обновляем сессию
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        
        $content .= "<p class='success'>✅ Роль пользователя обновлена на 'admin'.</p>";
        $content .= "<p class='success'>✅ Сессия обновлена:</p>";
        $content .= "<ul>";
        $content .= "<li>ID пользователя: " . $_SESSION['user_id'] . "</li>";
        $content .= "<li>Email пользователя: " . $_SESSION['user_email'] . "</li>";
        $content .= "<li>Роль пользователя: " . $_SESSION['user_role'] . "</li>";
        $content .= "</ul>";
    } else {
        $content .= "<p class='error'>❌ Ошибка: Пользователь не найден в базе данных!</p>";
    }
} else {
    $content .= "<p class='warning'>⚠️ Вы не авторизованы. Войдите в систему, а затем снова запустите этот скрипт.</p>";
    $content .= "<p><a href='/login' class='button'>Войти в систему</a></p>";
}

$content .= "<hr>";
$content .= "<h3>Что делать дальше:</h3>";
$content .= "<ol>";
$content .= "<li>Если вы видите все галочки ✅ - все настроено правильно.</li>";
$content .= "<li>Если вы видите, что не авторизованы, сначала войдите в систему, потом запустите скрипт снова.</li>";
$content .= "<li>Теперь у вас должен быть доступ к <a href='/admin'>админке</a>.</li>";
$content .= "<li>Также вы теперь можете обновлять данные в <a href='/account'>профиле</a>.</li>";
$content .= "</ol>";

// Отображаем страницу
showPage($content);
?> 