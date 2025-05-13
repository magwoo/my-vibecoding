<?php
// Debug script for admin 403 error
session_start();

// Function to try database connection
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

// Check if this is a fix request
$fixApplied = false;
if (isset($_POST['fix_session']) && isset($_SESSION['user_id'])) {
    $dbResult = tryDbConnection();
    if ($dbResult['success']) {
        $pdo = $dbResult['pdo'];
        $userId = $_SESSION['user_id'];
        
        // Update user role in database
        $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
        $stmt->execute([$userId]);
        
        // Rebuild session from scratch
        session_unset();
        
        // Get updated user data
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            // Force session write
            session_write_close();
            session_start();
            $fixApplied = true;
        }
    }
}

// Generate random token for admin override
$token = bin2hex(random_bytes(16));
if (isset($_POST['override_admin'])) {
    $_SESSION['user_role'] = 'admin';
    $_SESSION['admin_override'] = true;
    
    // Force session write
    session_write_close();
    session_start();
    $fixApplied = true;
}

// Graceful logout if requested
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: /login");
    exit;
}

// EMERGENCY: First-time setup mode (create admin user)
$emergencyMode = false;
$emergencyError = '';
$emergencySuccess = '';

if (isset($_POST['emergency_setup'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $emergencyError = "Email and password are required";
    } else {
        $dbResult = tryDbConnection();
        if ($dbResult['success']) {
            $pdo = $dbResult['pdo'];
            
            // Check if user exists
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $existingUser = $stmt->fetch();
            
            if ($existingUser) {
                // Update existing user to admin
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET role = 'admin', password = ? WHERE id = ?");
                $stmt->execute([$passwordHash, $existingUser['id']]);
                
                // Login as this user
                $_SESSION['user_id'] = $existingUser['id'];
                $_SESSION['user_email'] = $existingUser['email'];
                $_SESSION['user_role'] = 'admin';
                
                // Force session write
                session_write_close();
                session_start();
                $emergencySuccess = "Existing user updated and logged in as admin";
            } else {
                // Create new admin user
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (email, password, role, created_at, updated_at) VALUES (?, ?, 'admin', NOW(), NOW())");
                $stmt->execute([$email, $passwordHash]);
                $userId = $pdo->lastInsertId();
                
                // Login as this user
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = 'admin';
                
                // Force session write
                session_write_close();
                session_start();
                $emergencySuccess = "New admin user created and logged in";
            }
        } else {
            $emergencyError = "Could not connect to database";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Access Debugging</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1, h2, h3 {
            color: #333;
        }
        code {
            background-color: #f0f0f0;
            padding: 2px 5px;
            border-radius: 3px;
        }
        pre {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .error-box {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .success-box {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .warning-box {
            background-color: #fff3cd;
            color: #856404;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .step-box {
            background-color: #e2e3e5;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            margin: 5px 0;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            border: none;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-success {
            background-color: #28a745;
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
        .tab-container {
            margin-top: 20px;
        }
        .tab-buttons {
            display: flex;
            border-bottom: 1px solid #ddd;
        }
        .tab-button {
            padding: 10px 15px;
            background-color: #f2f2f2;
            border: none;
            cursor: pointer;
            margin-right: 5px;
            border-radius: 4px 4px 0 0;
        }
        .tab-button.active {
            background-color: #007bff;
            color: white;
        }
        .tab-content {
            padding: 15px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .tab-panel {
            display: none;
        }
        .tab-panel.active {
            display: block;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .emergency-mode {
            background-color: #f8d7da;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Access Debugging Tool</h1>
        
        <?php if ($fixApplied): ?>
            <div class="success-box">
                <p><strong>Fix applied!</strong> Your session has been updated. Try to access the admin panel now.</p>
                <p><a href="/admin" class="btn btn-success">Go to Admin Panel</a></p>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($emergencyError)): ?>
            <div class="error-box"><?= $emergencyError ?></div>
        <?php endif; ?>
        
        <?php if (!empty($emergencySuccess)): ?>
            <div class="success-box">
                <p><strong>Success!</strong> <?= $emergencySuccess ?></p>
                <p><a href="/admin" class="btn btn-success">Go to Admin Panel</a></p>
            </div>
        <?php endif; ?>
        
        <div class="tab-container">
            <div class="tab-buttons">
                <button class="tab-button active" onclick="openTab(event, 'tab-diagnosis')">Diagnosis</button>
                <button class="tab-button" onclick="openTab(event, 'tab-solutions')">Solutions</button>
                <button class="tab-button" onclick="openTab(event, 'tab-advanced')">Advanced</button>
                <button class="tab-button" onclick="openTab(event, 'tab-emergency')">Emergency</button>
            </div>
            
            <div id="tab-diagnosis" class="tab-panel active">
                <h2>Current Session Status</h2>
                <?php if (empty($_SESSION)): ?>
                    <div class="error-box">
                        <p><strong>No active session found.</strong> You need to log in first.</p>
                        <p><a href="/login" class="btn">Go to Login Page</a></p>
                    </div>
                <?php else: ?>
                    <h3>Session Variables:</h3>
                    <pre><?php print_r($_SESSION); ?></pre>
                    
                    <?php
                    // Check for common issues
                    $issues = [];
                    
                    if (!isset($_SESSION['user_id'])) {
                        $issues[] = 'User ID missing from session';
                    }
                    
                    if (!isset($_SESSION['user_role'])) {
                        $issues[] = 'User role missing from session';
                    } elseif ($_SESSION['user_role'] !== 'admin') {
                        $issues[] = 'User role is not set to "admin" (current value: ' . $_SESSION['user_role'] . ')';
                    }
                    
                    if (!isset($_SESSION['user_email'])) {
                        $issues[] = 'User email missing from session';
                    }
                    
                    // Connect to DB and check user data if possible
                    $dbResult = tryDbConnection();
                    if ($dbResult['success'] && isset($_SESSION['user_id'])) {
                        $pdo = $dbResult['pdo'];
                        
                        // Check user data in database
                        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                        $stmt->execute([$_SESSION['user_id']]);
                        $user = $stmt->fetch();
                        
                        if (!$user) {
                            $issues[] = 'User ID ' . $_SESSION['user_id'] . ' not found in database';
                        } else {
                            echo '<h3>User Data in Database:</h3>';
                            echo '<table>';
                            echo '<tr><th>Field</th><th>Value</th></tr>';
                            foreach ($user as $key => $value) {
                                if (!is_numeric($key)) {
                                    echo '<tr><td>' . $key . '</td><td>' . $value . '</td></tr>';
                                }
                            }
                            echo '</table>';
                            
                            // Check for mismatches
                            if (isset($_SESSION['user_email']) && $_SESSION['user_email'] !== $user['email']) {
                                $issues[] = 'Session email (' . $_SESSION['user_email'] . ') doesn\'t match database (' . $user['email'] . ')';
                            }
                            
                            if (isset($_SESSION['user_role']) && $_SESSION['user_role'] !== $user['role']) {
                                $issues[] = 'Session role (' . $_SESSION['user_role'] . ') doesn\'t match database (' . $user['role'] . ')';
                            }
                            
                            if ($user['role'] !== 'admin') {
                                $issues[] = 'User role in database is not set to "admin" (current value: ' . $user['role'] . ')';
                            }
                        }
                    }
                    
                    if (!empty($issues)) {
                        echo '<h3>Detected Issues:</h3>';
                        echo '<div class="error-box"><ul>';
                        foreach ($issues as $issue) {
                            echo '<li>' . $issue . '</li>';
                        }
                        echo '</ul></div>';
                    } else {
                        echo '<div class="success-box"><p>No obvious session issues detected. Your session appears to be properly configured for admin access.</p></div>';
                    }
                    ?>
                    
                    <h3>Session Cookie</h3>
                    <?php
                    $sessionCookie = [];
                    foreach ($_COOKIE as $name => $value) {
                        if ($name === session_name()) {
                            $sessionCookie[$name] = $value;
                        }
                    }
                    if (!empty($sessionCookie)) {
                        echo '<pre>';
                        print_r($sessionCookie);
                        echo '</pre>';
                    } else {
                        echo '<div class="error-box"><p>No session cookie found - this may indicate a cookie problem.</p></div>';
                    }
                    ?>
                <?php endif; ?>
            </div>
            
            <div id="tab-solutions" class="tab-panel">
                <h2>Potential Solutions</h2>
                
                <?php if (empty($_SESSION)): ?>
                    <div class="step-box">
                        <h3>Step 1: Log In First</h3>
                        <p>You need to be logged in before you can access the admin panel.</p>
                        <a href="/login" class="btn">Go to Login Page</a>
                        <a href="/login_debug.php" class="btn btn-success">Use Debug Login Form</a>
                    </div>
                <?php else: ?>
                    <div class="step-box">
                        <h3>Step 1: Fix Session Data</h3>
                        <p>This will update your database role to 'admin' and refresh your session variables.</p>
                        <form method="post" action="">
                            <input type="hidden" name="fix_session" value="1">
                            <button type="submit" class="btn btn-success">Apply Fix</button>
                        </form>
                    </div>
                    
                    <div class="step-box">
                        <h3>Step 2: Try Different Tools</h3>
                        <p>We have created several tools to help diagnose and fix admin access issues.</p>
                        <a href="/admin_access_fix.php" class="btn">Use Admin Access Fix Tool</a>
                        <a href="/check_php_config.php" class="btn">Check PHP Configuration</a>
                    </div>
                    
                    <div class="step-box">
                        <h3>Step 3: Clear Cookies and Try Again</h3>
                        <p>Sometimes cookies can cause session issues. Try logging out, clearing your browser cookies, and logging in again.</p>
                        <form method="post" action="">
                            <input type="hidden" name="logout" value="1">
                            <button type="submit" class="btn btn-danger">Logout</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            
            <div id="tab-advanced" class="tab-panel">
                <h2>Advanced Options</h2>
                
                <div class="warning-box">
                    <p><strong>Warning:</strong> These options should only be used as a last resort when other fixes don't work.</p>
                </div>
                
                <?php if (!empty($_SESSION)): ?>
                    <div class="step-box">
                        <h3>Force Admin Role Override</h3>
                        <p>This will forcibly set your session role to 'admin' regardless of database settings. This is a temporary solution.</p>
                        <form method="post" action="">
                            <input type="hidden" name="override_admin" value="1">
                            <button type="submit" class="btn btn-danger">Force Admin Role</button>
                        </form>
                    </div>
                    
                    <div class="step-box">
                        <h3>Add Debug Parameter to URLs</h3>
                        <p>Add these query parameters to see more debugging information:</p>
                        <ul>
                            <li><a href="/admin?debug_session=show">?debug_session=show</a> - Shows session information</li>
                            <li><a href="/admin?fix_admin_role=1">?fix_admin_role=1</a> - Tries to fix admin role on the fly</li>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <div class="step-box">
                    <h3>Direct Access URL with Debug</h3>
                    <p>Try accessing the admin page with debug parameters:</p>
                    <a href="/admin?debug_session=show" class="btn">Admin with Session Debug</a>
                </div>
            </div>
            
            <div id="tab-emergency" class="tab-panel">
                <h2>Emergency Setup</h2>
                
                <div class="warning-box">
                    <p><strong>Warning:</strong> Use this only if you need to create or reset an admin account when everything else fails.</p>
                </div>
                
                <div class="emergency-mode">
                    <h3>Create/Reset Admin User</h3>
                    <p>This will create a new admin user or update an existing one with admin privileges.</p>
                    
                    <form method="post" action="">
                        <div>
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div>
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        
                        <input type="hidden" name="emergency_setup" value="1">
                        <button type="submit" class="btn btn-danger">Create/Reset Admin User</button>
                    </form>
                </div>
            </div>
        </div>
        
        <h2>Navigation</h2>
        <p>
            <a href="/admin" class="btn">Try Admin Panel</a>
            <a href="/admin_access_fix.php" class="btn">Admin Access Fix Tool</a>
            <a href="/login_debug.php" class="btn">Debug Login Form</a>
            <a href="/" class="btn">Go to Home Page</a>
        </p>
    </div>
    
    <script>
    function openTab(evt, tabId) {
        var i, tabPanels, tabButtons;
        
        // Hide all tab panels
        tabPanels = document.getElementsByClassName("tab-panel");
        for (i = 0; i < tabPanels.length; i++) {
            tabPanels[i].classList.remove("active");
        }
        
        // Remove active class from all tab buttons
        tabButtons = document.getElementsByClassName("tab-button");
        for (i = 0; i < tabButtons.length; i++) {
            tabButtons[i].classList.remove("active");
        }
        
        // Show the current tab and add active class to the button
        document.getElementById(tabId).classList.add("active");
        evt.currentTarget.classList.add("active");
    }
    </script>
</body>
</html> 