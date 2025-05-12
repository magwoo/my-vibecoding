<?php
// Debug script to verify header is loading properly

// Include necessary files
require_once __DIR__ . '/../src/bootstrap.php';

// Print some debug information
echo '<h1>Debug Headers and Template Info</h1>';

// Check if the Cart model is correctly loaded
echo '<h2>Cart Model Check</h2>';
if (class_exists('\App\Models\Cart')) {
    echo '<p style="color:green;">Cart model class is loaded correctly</p>';
} else {
    echo '<p style="color:red;">Cart model class is NOT loaded!</p>';
    
    // Check if the Cart.php file exists
    if (file_exists(SRC_DIR . '/Models/Cart.php')) {
        echo '<p>The Cart.php file exists at ' . SRC_DIR . '/Models/Cart.php</p>';
        
        // Try to include it
        require_once SRC_DIR . '/Models/Cart.php';
        if (class_exists('\App\Models\Cart')) {
            echo '<p style="color:green;">Cart model class has been successfully loaded after direct include</p>';
        } else {
            echo '<p style="color:red;">Cart model class STILL not loaded after direct include!</p>';
        }
    } else {
        echo '<p style="color:red;">The Cart.php file does NOT exist at the expected location!</p>';
    }
}

// Check for Product model
echo '<h2>Product Model Check</h2>';
if (class_exists('\App\Models\Product')) {
    echo '<p style="color:green;">Product model class is loaded correctly</p>';
} else {
    echo '<p style="color:red;">Product model class is NOT loaded!</p>';
}

// Check if the header file exists
echo '<h2>Template Files Check</h2>';
$headerFile = VIEWS_DIR . '/layouts/header.php';
$footerFile = VIEWS_DIR . '/layouts/footer.php';

if (file_exists($headerFile)) {
    echo '<p style="color:green;">Header file exists at: ' . $headerFile . '</p>';
} else {
    echo '<p style="color:red;">Header file does NOT exist at: ' . $headerFile . '</p>';
}

if (file_exists($footerFile)) {
    echo '<p style="color:green;">Footer file exists at: ' . $footerFile . '</p>';
} else {
    echo '<p style="color:red;">Footer file does NOT exist at: ' . $footerFile . '</p>';
}

// Show constants
echo '<h2>Constants</h2>';
echo '<p>VIEWS_DIR: ' . VIEWS_DIR . '</p>';
echo '<p>ROOT_DIR: ' . ROOT_DIR . '</p>';
echo '<p>SRC_DIR: ' . SRC_DIR . '</p>';

// Show session info
echo '<h2>Session Info</h2>';
if (isset($_SESSION) && !empty($_SESSION)) {
    echo '<pre>';
    print_r($_SESSION);
    echo '</pre>';
} else {
    echo '<p>No active session data found.</p>';
} 