<?php
// Скрипт для полной настройки проекта
session_start();

// Параметры подключения к базе данных
$host = 'localhost'; // Меняем с mysql на localhost
$db = 'phone_store';
$user = 'root'; // Стандартный пользователь для локальной разработки
$password = ''; // Пустой пароль для локальной разработки
$charset = 'utf8mb4';

try {
    // Подключение к базе данных
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $user, $password, $options);
    echo "<h2>Подключение к базе данных выполнено успешно!</h2>";
    
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
        echo "<p>✅ Добавлены необходимые поля в таблицу пользователей.</p>";
    } else {
        echo "<p>✅ Поля в таблице пользователей уже существуют.</p>";
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
            
            echo "<p>✅ Роль пользователя обновлена на 'admin'.</p>";
            echo "<p>✅ Сессия обновлена:</p>";
            echo "<ul>";
            echo "<li>ID пользователя: " . $_SESSION['user_id'] . "</li>";
            echo "<li>Email пользователя: " . $_SESSION['user_email'] . "</li>";
            echo "<li>Роль пользователя: " . $_SESSION['user_role'] . "</li>";
            echo "</ul>";
        } else {
            echo "<p>❌ Ошибка: Пользователь не найден в базе данных!</p>";
        }
    } else {
        echo "<p>⚠️ Вы не авторизованы. Войдите в систему, а затем снова запустите этот скрипт.</p>";
    }
    
    echo "<hr>";
    echo "<h3>Что делать дальше:</h3>";
    echo "<ol>";
    echo "<li>Если вы видите все галочки ✅ - все настроено правильно.</li>";
    echo "<li>Если вы видите, что не авторизованы, сначала войдите в систему, потом запустите скрипт снова.</li>";
    echo "<li>Теперь у вас должен быть доступ к <a href='/admin'>админке</a>.</li>";
    echo "<li>Также вы теперь можете обновлять данные в <a href='/account'>профиле</a>.</li>";
    echo "</ol>";
    
} catch (PDOException $e) {
    echo "<h2>Ошибка базы данных:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    
    // Проверяем, есть ли ошибка доступа к базе
    if (strpos($e->getMessage(), "Access denied") !== false) {
        echo "<p>Похоже, не удается подключиться к базе данных с указанными параметрами. Отредактируйте файл <strong>fix_all.php</strong> и укажите правильные параметры подключения:</p>";
        echo "<pre>
\$host = 'localhost'; // Имя хоста базы данных
\$db = 'phone_store'; // Имя базы данных
\$user = 'root';      // Имя пользователя
\$password = '';      // Пароль
</pre>";
    }
}
?> 