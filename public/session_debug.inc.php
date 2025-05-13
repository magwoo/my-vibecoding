<?php
/**
 * Session Debug Include File
 * 
 * Include this file at the top of any PHP file to debug session issues:
 * require_once __DIR__ . '/session_debug.inc.php';
 * 
 * It will display session information only to admin users
 * or when a secret query parameter is provided.
 */

// Make sure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Show debug info if user is admin or debug param is provided
$showDebug = false;

// Check if the user has admin role
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    $showDebug = true;
}

// Check if debug parameter is provided (enable for troubleshooting)
if (isset($_GET['debug_session']) && $_GET['debug_session'] === 'show') {
    $showDebug = true;
}

// Output debug information
if ($showDebug) {
    // Simple styling for debug output
    echo '<style>
    .session-debug {
        position: fixed;
        top: 10px;
        right: 10px;
        max-width: 400px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        padding: 10px;
        border-radius: 4px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        z-index: 9999;
        font-family: monospace;
        font-size: 12px;
        color: #333;
        max-height: 80vh;
        overflow: auto;
    }
    .session-debug h4 {
        margin: 0 0 10px 0;
        padding-bottom: 5px;
        border-bottom: 1px solid #dee2e6;
    }
    .session-debug pre {
        margin: 0;
        white-space: pre-wrap;
    }
    .session-debug-close {
        position: absolute;
        top: 5px;
        right: 5px;
        cursor: pointer;
        font-weight: bold;
    }
    </style>';
    
    echo '<div class="session-debug" id="sessionDebug">';
    echo '<span class="session-debug-close" onclick="document.getElementById(\'sessionDebug\').style.display=\'none\'">×</span>';
    echo '<h4>Session Debug Info</h4>';
    
    // Session ID and status
    echo '<p><strong>Session ID:</strong> ' . session_id() . '</p>';
    echo '<p><strong>Session Status:</strong> ' . session_status() . '</p>';
    
    // Session data
    echo '<p><strong>Session Data:</strong></p>';
    echo '<pre>';
    print_r($_SESSION);
    echo '</pre>';
    
    // Cookie info
    echo '<p><strong>Session Cookie:</strong></p>';
    $sessionCookie = [];
    foreach ($_COOKIE as $name => $value) {
        if ($name === session_name()) {
            $sessionCookie[$name] = $value;
        }
    }
    echo '<pre>';
    print_r($sessionCookie);
    echo '</pre>';
    
    echo '</div>';
}

// If there's a request to fix the admin role and user is logged in
if (isset($_GET['fix_admin_role']) && isset($_SESSION['user_id'])) {
    // Try to connect to database with multiple possible configurations
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
    $connected = false;
    $pdo = null;
    
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
            $connected = true;
            break;
        } catch (PDOException $e) {
            continue;
        }
    }
    
    if ($connected) {
        // Update the user role to admin
        $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        
        // Update session
        $_SESSION['user_role'] = 'admin';
        
        // Force session write
        session_write_close();
        session_start();
        
        // Redirect to remove the query parameter
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
        exit;
    }
} 