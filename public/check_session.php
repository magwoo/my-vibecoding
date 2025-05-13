<?php
// Скрипт для проверки и исправления сессии
session_start();

// Вывод текущей сессии
echo "<h2>Текущая сессия:</h2>";
echo "<pre>";
var_dump($_SESSION);
echo "</pre>";

// Попытка авто-исправления сессии user_role
if (isset($_SESSION['user_id']) && !isset($_SESSION['user_role'])) {
    echo "<h2>Исправляем сессию...</h2>";
    
    // Параметры подключения к базе данных - пробуем несколько вариантов
    $possibleHosts = ['localhost', 'mysql', '127.0.0.1', 'db'];
    $db = 'phone_store';
    $possibleUsers = ['root', 'phone_store'];
    $possiblePasswords = ['', 'root', 'password', 'phone_store_password'];
    $charset = 'utf8mb4';
    
    $connected = false;
    
    foreach ($possibleHosts as $host) {
        foreach ($possibleUsers as $user) {
            foreach ($possiblePasswords as $password) {
                try {
                    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
                    $options = [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::ATTR_TIMEOUT => 1, // Короткий таймаут для быстрой проверки
                    ];
                    
                    $pdo = new PDO($dsn, $user, $password, $options);
                    $connected = true;
                    echo "<p>Успешно подключились к БД: хост=$host, пользователь=$user</p>";
                    break 3; // Выходим из всех циклов, если подключились
                } catch (PDOException $e) {
                    // Продолжаем со следующей комбинацией
                    continue;
                }
            }
        }
    }
    
    if ($connected) {
        // Получаем информацию о пользователе
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Обновляем сессию
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                echo "<p style='color: green;'>✅ Сессия успешно обновлена с данными из базы!</p>";
                echo "<ul>";
                echo "<li>ID пользователя: " . $_SESSION['user_id'] . "</li>";
                echo "<li>Email пользователя: " . $_SESSION['user_email'] . "</li>";
                echo "<li>Роль пользователя: " . $_SESSION['user_role'] . "</li>";
                echo "</ul>";
                
                // Установка роли admin, если её нет
                if ($_SESSION['user_role'] !== 'admin') {
                    $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $_SESSION['user_role'] = 'admin';
                    echo "<p style='color: green;'>✅ Роль пользователя обновлена на 'admin'</p>";
                }
            } else {
                echo "<p style='color: red;'>❌ Пользователь не найден в базе данных!</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color: red;'>❌ Ошибка при обновлении сессии: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Не удалось подключиться к базе данных. Попробуйте открыть скрипт /migrations/fix_admin.php</p>";
    }
}

// Форма для перезапуска сессии
echo "<h2>Действия:</h2>";
echo "<form method='post' action=''>";
echo "<input type='submit' name='logout' value='Выйти из системы (Logout)' style='padding: 10px; margin-right: 10px; background-color: #f44336; color: white; border: none; cursor: pointer;'>";
echo "<a href='/admin' style='padding: 10px; background-color: #4CAF50; color: white; text-decoration: none;'>Попробовать открыть админку</a>";
echo "</form>";

// Обработка выхода из системы
if (isset($_POST['logout'])) {
    // Уничтожаем сессию
    session_unset();
    session_destroy();
    // Перенаправляем на страницу логина
    header("Location: /login");
    exit;
}
?> 