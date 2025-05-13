<?php
// Script to fix admin session issues comprehensively
session_start();

// Setup page styles
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Access Fix</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1, h2, h3 {
            color: #333;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .warning {
            background-color: #fff3cd;
            color: #856404;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        pre {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
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
        .btn-success {
            background-color: #28a745;
        }
        form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Access Fix</h1>

        <?php
        // Show session info
        echo '<h2>Current Session Information</h2>';
        
        if (!empty($_SESSION)) {
            echo '<pre>';
            print_r($_SESSION);
            echo '</pre>';
        } else {
            echo '<div class="warning">You are not logged in. Session is empty.</div>';
        }

        // Function to try database connection with multiple possible configurations
        function tryDbConnection() {
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
                        PDO::ATTR_TIMEOUT => 2,
                    ];
                    
                    $pdo = new PDO($dsn, $config['user'], $config['password'], $options);
                    return [
                        'success' => true,
                        'pdo' => $pdo,
                        'config' => $config
                    ];
                } catch (PDOException $e) {
                    continue;
                }
            }
            
            return ['success' => false, 'message' => 'Could not connect to the database with any configuration'];
        }

        // Try to connect to the database
        $dbResult = tryDbConnection();

        if (!$dbResult['success']) {
            echo '<div class="error">'.$dbResult['message'].'</div>';
        } else {
            $pdo = $dbResult['pdo'];
            $config = $dbResult['config'];
            
            echo '<div class="success">Successfully connected to database using: Host='.$config['host'].', User='.$config['user'].'</div>';
            
            // Check if user is logged in
            if (isset($_SESSION['user_id'])) {
                $userId = $_SESSION['user_id'];
                
                // Get user data from database
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch();
                
                if ($user) {
                    echo '<h2>User Information from Database</h2>';
                    echo '<table>';
                    echo '<tr><th>ID</th><td>'.$user['id'].'</td></tr>';
                    echo '<tr><th>Email</th><td>'.$user['email'].'</td></tr>';
                    echo '<tr><th>Role</th><td>'.$user['role'].'</td></tr>';
                    echo '</table>';
                    
                    // Check session vs database discrepancies
                    $issues = [];
                    
                    if (!isset($_SESSION['user_email']) || $_SESSION['user_email'] !== $user['email']) {
                        $issues[] = 'Session email is missing or doesn\'t match database';
                    }
                    
                    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $user['role']) {
                        $issues[] = 'Session role is missing or doesn\'t match database';
                    }
                    
                    if ($user['role'] !== 'admin') {
                        $issues[] = 'User role in database is not set to admin';
                    }
                    
                    if (empty($issues)) {
                        echo '<div class="success">No session issues detected. Your user account and session are properly configured.</div>';
                    } else {
                        echo '<div class="error"><strong>Session Issues Detected:</strong><ul>';
                        foreach ($issues as $issue) {
                            echo '<li>'.$issue.'</li>';
                        }
                        echo '</ul></div>';
                        
                        // Fix button
                        echo '<form method="post" action="">';
                        echo '<input type="hidden" name="fix_session" value="1">';
                        echo '<button type="submit" class="btn btn-success">Fix All Issues</button>';
                        echo '</form>';
                    }
                } else {
                    echo '<div class="error">User ID '.$userId.' not found in the database!</div>';
                }
            } else {
                echo '<div class="warning">You are not logged in. Please log in first.</div>';
                echo '<p><a href="/login" class="btn">Go to Login Page</a>';
                echo '<a href="/login_debug.php" class="btn btn-success">Use Debug Login Form</a></p>';
            }
        }
        
        // Process fix request
        if (isset($_POST['fix_session']) && isset($_SESSION['user_id'])) {
            if (isset($pdo)) {
                $userId = $_SESSION['user_id'];
                
                // 1. Update user role in database
                $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
                $stmt->execute([$userId]);
                
                // 2. Fetch updated user data
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch();
                
                if ($user) {
                    // 3. Update session variables
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    // 4. Check for PHP session storage/cookie issues
                    if (ini_get('session.use_cookies')) {
                        $params = session_get_cookie_params();
                        setcookie(session_name(), session_id(), time() + 86400, 
                            $params['path'], $params['domain'], 
                            $params['secure'], $params['httponly']
                        );
                    }
                    
                    // 5. Forcibly write session data
                    session_write_close();
                    session_start();
                    
                    echo '<div class="success">Session and user data have been fixed! <br>
                        - User role set to admin in database <br>
                        - Session variables updated <br>
                        - Session cookie refreshed <br>
                        Refresh this page to verify the changes.</div>';
                }
            }
        }
        
        // Add navigation links
        echo '<h2>Navigation</h2>';
        echo '<p>';
        echo '<a href="/admin" class="btn btn-success">Try Admin Panel</a>';
        echo '<a href="/account" class="btn">Go to Profile</a>';
        
        if (isset($_SESSION['user_id'])) {
            echo '<form method="post" action="" style="display:inline;">';
            echo '<input type="hidden" name="logout" value="1">';
            echo '<button type="submit" class="btn btn-danger">Logout</button>';
            echo '</form>';
        }
        echo '</p>';
        
        // Handle logout
        if (isset($_POST['logout'])) {
            session_unset();
            session_destroy();
            header("Location: /login");
            exit;
        }
        ?>
        
        <h2>Additional Troubleshooting</h2>
        <div class="info">
            <p>If you still can't access the admin panel after running this fix:</p>
            <ol>
                <li>Make sure you're using the same browser session between login and accessing the admin panel</li>
                <li>Check if your browser accepts cookies</li>
                <li>Try clearing your browser cache and cookies</li>
                <li>Use the debug login form (/login_debug.php) to ensure proper login</li>
                <li>Try using a private/incognito browser window</li>
            </ol>
        </div>
    </div>
</body>
</html> 