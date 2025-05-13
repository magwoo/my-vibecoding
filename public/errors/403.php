<?php
// Custom 403 error page with debugging information
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Forbidden</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1, h2, h3 {
            color: #333;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .info-message {
            background-color: #d1ecf1;
            color: #0c5460;
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
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .debug-section {
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .debug-toggle {
            cursor: pointer;
            color: #007bff;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-message">
            <h1>403 - Access Forbidden</h1>
            <p>Sorry, you do not have permission to access this page.</p>
        </div>
        
        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="info-message">
            <p>You are logged in, but you don't have admin privileges required to access this page.</p>
        </div>
        <?php else: ?>
        <div class="info-message">
            <p>You need to log in with an admin account to access this page.</p>
            <p><a href="/login" class="btn">Go to Login</a></p>
        </div>
        <?php endif; ?>
        
        <div>
            <p><a href="/" class="btn">Return to Home</a></p>
            
            <?php if (isset($_SESSION['user_id'])): ?>
            <p>
                <a href="/admin_403_debug.php" class="btn">Debug Admin Access</a>
                <a href="/admin?fix_role=true" class="btn">Try to Fix Role</a>
                <a href="/admin?admin_override=true" class="btn btn-danger">Force Admin Access</a>
            </p>
            <?php endif; ?>
        </div>
        
        <div class="debug-section">
            <h2><span class="debug-toggle" onclick="toggleDebug()">Show Debug Information</span></h2>
            
            <div id="debug-info" style="display: none;">
                <h3>Session Information</h3>
                <?php if (!empty($_SESSION)): ?>
                <pre><?php print_r($_SESSION); ?></pre>
                <?php else: ?>
                <p>No session information available.</p>
                <?php endif; ?>
                
                <h3>Database Check</h3>
                <?php
                // Try to check the database
                if (isset($_SESSION['user_id'])) {
                    try {
                        // Try different database configurations
                        $possibleConfigs = [
                            ['host' => 'localhost', 'user' => 'root', 'password' => ''],
                            ['host' => 'localhost', 'user' => 'root', 'password' => 'root'],
                            ['host' => 'mysql', 'user' => 'root', 'password' => ''],
                            ['host' => 'mysql', 'user' => 'phone_store', 'password' => 'phone_store_password']
                        ];
                        
                        $connected = false;
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
                                $connected = true;
                                
                                // Get user from database
                                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                                $stmt->execute([$_SESSION['user_id']]);
                                $user = $stmt->fetch();
                                
                                if ($user) {
                                    echo '<table>';
                                    echo '<tr><th>Field</th><th>Value</th></tr>';
                                    foreach ($user as $key => $value) {
                                        if (!is_numeric($key)) {
                                            echo '<tr><td>' . $key . '</td><td>' . $value . '</td></tr>';
                                        }
                                    }
                                    echo '</table>';
                                    
                                    // Check specific issues
                                    if ($user['role'] !== 'admin') {
                                        echo '<p>⚠️ <strong>Issue detected:</strong> Your role in the database is "' . $user['role'] . '", but it should be "admin".</p>';
                                    }
                                } else {
                                    echo '<p>⚠️ <strong>Issue detected:</strong> User with ID ' . $_SESSION['user_id'] . ' not found in database.</p>';
                                }
                                
                                break; // Found working connection
                            } catch (PDOException $e) {
                                // Try next configuration
                                continue;
                            }
                        }
                        
                        if (!$connected) {
                            echo '<p>Could not connect to the database with any configuration.</p>';
                        }
                    } catch (Exception $e) {
                        echo '<p>Error checking database: ' . $e->getMessage() . '</p>';
                    }
                } else {
                    echo '<p>No user session to check in database.</p>';
                }
                ?>
                
                <h3>Quick Fixes</h3>
                <p>Try these options to fix common issues:</p>
                <ol>
                    <li><a href="/login_debug.php">Use debug login form</a> to ensure proper login</li>
                    <li><a href="/admin_access_fix.php">Use the admin access fix tool</a> to check and fix session issues</li>
                    <li><a href="/check_php_config.php">Check PHP configuration</a> for session-related issues</li>
                    <li><a href="/admin?debug_mode=1">Access admin panel with debug mode</a> to see more detailed information</li>
                </ol>
            </div>
        </div>
    </div>
    
    <script>
        function toggleDebug() {
            var debugInfo = document.getElementById('debug-info');
            var display = debugInfo.style.display;
            debugInfo.style.display = display === 'none' ? 'block' : 'none';
            
            var toggleText = document.querySelector('.debug-toggle');
            toggleText.textContent = display === 'none' ? 'Hide Debug Information' : 'Show Debug Information';
        }
    </script>
</body>
</html>
