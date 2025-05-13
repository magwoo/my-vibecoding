<?php
// Script to check PHP and session configuration
session_start();

// Style for better output
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Configuration Check</title>
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
        .warning {
            background-color: #fff3cd;
            color: #856404;
            padding: 5px;
        }
        .ok {
            background-color: #d4edda;
            color: #155724;
            padding: 5px;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>PHP Configuration Check</h1>';

// Check PHP version
$phpVersion = phpversion();
echo '<h2>PHP Version</h2>';
echo '<p>PHP Version: <strong>' . $phpVersion . '</strong></p>';

// Check session configuration
echo '<h2>Session Configuration</h2>';
echo '<table>';
echo '<tr><th>Setting</th><th>Value</th><th>Status</th></tr>';

$sessionSettings = [
    'session.save_handler' => [
        'description' => 'Where sessions are stored',
        'recommended' => 'files'
    ],
    'session.save_path' => [
        'description' => 'Path where sessions are stored',
        'check' => function($value) {
            return !empty($value) && is_writable($value);
        }
    ],
    'session.use_cookies' => [
        'description' => 'Use cookies to store session ID',
        'recommended' => '1',
        'warning' => 'Sessions may not work properly without cookies'
    ],
    'session.use_only_cookies' => [
        'description' => 'Use only cookies for session ID',
        'recommended' => '1',
        'warning' => 'Security risk if not enabled'
    ],
    'session.cookie_lifetime' => [
        'description' => 'Lifetime of session cookie (seconds)',
        'warning' => function($value) {
            return (int)$value < 3600 ? 'Short session lifetime may cause frequent logouts' : null;
        }
    ],
    'session.cookie_path' => [
        'description' => 'Path for session cookies',
        'recommended' => '/'
    ],
    'session.cookie_domain' => [
        'description' => 'Domain for session cookies'
    ],
    'session.cookie_secure' => [
        'description' => 'Only transmit cookies over HTTPS',
        'info' => 'Should be 1 in production with HTTPS'
    ],
    'session.cookie_httponly' => [
        'description' => 'Prevent JavaScript access to session cookie',
        'recommended' => '1',
        'warning' => 'Security risk if not enabled'
    ],
    'session.gc_maxlifetime' => [
        'description' => 'Session garbage collection lifetime (seconds)',
        'warning' => function($value) {
            return (int)$value < 1440 ? 'Short GC lifetime may cause sessions to expire too quickly' : null;
        }
    ]
];

foreach ($sessionSettings as $directive => $info) {
    $value = ini_get($directive);
    
    echo '<tr>';
    echo '<td>' . $directive . '<br><small>' . ($info['description'] ?? '') . '</small></td>';
    echo '<td>' . (empty($value) ? '<em>empty</em>' : $value) . '</td>';
    
    // Determine status
    $status = '';
    $statusClass = 'ok';
    
    if (isset($info['recommended']) && $value != $info['recommended']) {
        $status = 'Recommended: ' . $info['recommended'];
        $statusClass = 'warning';
    } else if (isset($info['check']) && !$info['check']($value)) {
        $status = 'Issue detected';
        $statusClass = 'warning';
    } else if (isset($info['warning'])) {
        if (is_callable($info['warning'])) {
            $warning = $info['warning']($value);
            if ($warning) {
                $status = $warning;
                $statusClass = 'warning';
            }
        } else if (!empty($info['warning'])) {
            $status = $info['warning'];
            $statusClass = 'warning';
        }
    }
    
    echo '<td class="' . $statusClass . '">' . $status . '</td>';
    echo '</tr>';
}

echo '</table>';

// Check session status
echo '<h2>Session Status</h2>';

$sessionStatus = session_status();
$sessionStatusText = [
    PHP_SESSION_DISABLED => 'Sessions are disabled',
    PHP_SESSION_NONE => 'Sessions are enabled but none exists',
    PHP_SESSION_ACTIVE => 'Sessions are enabled and one exists'
];

echo '<p>Current session status: <strong>' . $sessionStatusText[$sessionStatus] . '</strong></p>';

// Check session ID
echo '<p>Current session ID: <strong>' . session_id() . '</strong></p>';

// Check session directory permissions
$sessionSavePath = ini_get('session.save_path');
if (!empty($sessionSavePath)) {
    $savePath = explode(';', $sessionSavePath);
    $path = end($savePath);
    
    // Remove depth indicator if present
    if (preg_match('|^\d+;(.+)|', $path, $matches)) {
        $path = $matches[1];
    }
    
    if (is_dir($path)) {
        echo '<p>Session directory exists: <strong>Yes</strong></p>';
        echo '<p>Session directory writable: <strong>' . (is_writable($path) ? 'Yes' : 'No - This could be a problem!') . '</strong></p>';
    } else {
        echo '<p class="warning">Session directory does not exist or is not accessible: ' . $path . '</p>';
    }
} else {
    echo '<p class="warning">Session save path is not set!</p>';
}

// Check loaded PHP modules
echo '<h2>Loaded PHP Modules</h2>';
$loadedExtensions = get_loaded_extensions();
sort($loadedExtensions);

$importantExtensions = [
    'pdo', 'pdo_mysql', 'mysqli', 'session', 'json', 'mbstring'
];

echo '<p><strong>Important extensions:</strong> ';
foreach ($importantExtensions as $ext) {
    echo $ext . ': ' . (in_array($ext, $loadedExtensions) ? '<span class="ok">Loaded</span>' : '<span class="warning">Not loaded</span>') . ' | ';
}
echo '</p>';

// Output current working directory and permissions
echo '<h2>Environment Information</h2>';
echo '<p>Current working directory: <strong>' . getcwd() . '</strong></p>';

// PHP open_basedir restriction
$openBasedir = ini_get('open_basedir');
echo '<p>open_basedir restriction: <strong>' . (empty($openBasedir) ? 'None' : $openBasedir) . '</strong></p>';

// Session cookie parameters
echo '<h2>Session Cookie Parameters</h2>';
$params = session_get_cookie_params();
echo '<table>';
foreach ($params as $key => $value) {
    echo '<tr><td>' . $key . '</td><td>' . (is_bool($value) ? ($value ? 'true' : 'false') : $value) . '</td></tr>';
}
echo '</table>';

// Test session writing
echo '<h2>Session Write Test</h2>';

$testKey = 'test_' . time();
$_SESSION[$testKey] = 'Test value set at ' . date('Y-m-d H:i:s');

echo '<p>Test value set in session. Refresh page to verify if session writing works.</p>';

if (isset($_SESSION[$testKey])) {
    echo '<p>Previous test value found: <strong>' . $_SESSION[$testKey] . '</strong></p>';
}

// Navigation
echo '<h2>Navigation</h2>';
echo '<p>
    <a href="/admin_access_fix.php" class="btn">Admin Access Fix</a>
    <a href="/login_debug.php" class="btn">Debug Login</a>
    <a href="/admin" class="btn">Try Admin Panel</a>
</p>';

echo '</div></body></html>'; 