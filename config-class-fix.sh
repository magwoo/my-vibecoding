#!/bin/bash

# Create temporary directory
mkdir -p tmp

# Create updated Config.php file
cat > tmp/Config.php << 'EOF'
<?php

namespace App\Core;

class Config
{
    private static $config = null;
    
    private static function loadConfig()
    {
        if (self::$config === null) {
            self::$config = require SRC_DIR . '/config/config.php';
        }
    }
    
    public static function get($key)
    {
        self::loadConfig();
        
        $parts = explode(".", $key);
        
        $value = self::$config;
        foreach ($parts as $part) {
            if (!isset($value[$part])) {
                return null;
            }
            $value = $value[$part];
        }
        
        return $value;
    }
}
EOF

# Copy fixed Config.php to Docker container
docker cp tmp/Config.php phone-store-php-1:/var/www/html/src/Core/

# Restart PHP container
docker restart phone-store-php-1

# Clean up
rm -rf tmp 

echo "Config class fix applied successfully." 