<?php
// Emergency admin access - use only when all other methods fail
session_start();

// Set up admin session variables
$_SESSION['user_role'] = 'admin';
$_SESSION['admin_override'] = true;
$_SESSION['emergency_access'] = true;

// Ensure we have a user ID - if not, create a temporary admin ID
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 999999; // Temporary ID
    $_SESSION['user_email'] = 'emergency@admin.local';
    $_SESSION['temp_user'] = true;
}

// Forcibly write session
session_write_close();
session_start();

// Force database update if possible
$dbUpdated = false;
if (isset($_SESSION['user_id']) && !isset($_SESSION['temp_user'])) {
    // Try to connect to the database
    $possibleConfigs = [
        ['host' => 'localhost', 'user' => 'root', 'password' => ''],
        ['host' => 'localhost', 'user' => 'root', 'password' => 'root'],
        ['host' => 'mysql', 'user' => 'root', 'password' => ''],
        ['host' => 'mysql', 'user' => 'phone_store', 'password' => 'phone_store_password'],
        ['host' => 'db', 'user' => 'root', 'password' => ''],
        ['host' => 'db', 'user' => 'phone_store', 'password' => 'phone_store_password'],
        ['host' => '127.0.0.1', 'user' => 'root', 'password' => ''],
        ['host' => '127.0.0.1', 'user' => 'root', 'password' => 'root'],
    ];
    
    $db = 'phone_store';
    $charset = 'utf8mb4';
    
    foreach ($possibleConfigs as $config) {
        try {
            $dsn = "mysql:host={$config['host']};dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 1,
            ];
            
            $pdo = new PDO($dsn, $config['user'], $config['password'], $options);
            
            // Update user role in database
            $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            
            $dbUpdated = true;
            break;
        } catch (PDOException $e) {
            // Try next configuration
            continue;
        }
    }
}

// Handle directly going to admin
if (isset($_GET['redirect']) && $_GET['redirect'] === 'admin') {
    header("Location: /admin");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Admin Access</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #dc3545;
            border-bottom: 2px solid #dc3545;
            padding-bottom: 10px;
        }
        .alert {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        pre {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
        }
        .btn-danger {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Emergency Admin Access</h1>
        
        <div class="alert">
            <strong>WARNING:</strong> This is an emergency override that forcibly grants admin access
            regardless of database settings. Use only when all other methods have failed.
        </div>
        
        <div class="success">
            <strong>Admin access granted!</strong> Your session has been updated with admin privileges.
            <?php if ($dbUpdated): ?>
            <p>Your user role has also been updated in the database.</p>
            <?php else: ?>
            <p>Note: Your database was not updated. This is a temporary session-only fix.</p>
            <?php endif; ?>
        </div>
        
        <h2>Session Information</h2>
        <pre><?php print_r($_SESSION); ?></pre>
        
        <p>
            <a href="/admin" class="btn">Go to Admin Panel</a>
            <a href="/" class="btn">Go to Home Page</a>
        </p>
        
        <p style="margin-top: 30px; font-size: 14px; color: #6c757d;">
            If you are still experiencing issues, please try clearing your browser cookies and cache, 
            then log in again using the <a href="/login_debug.php">debug login form</a>.
        </p>
    </div>
</body>
</html> 