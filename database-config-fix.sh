#!/bin/bash

# Create temporary directory
mkdir -p tmp

# Create updated config.php file with correct database host
cat > tmp/config.php << 'EOF'
<?php

return [
    "app" => [
        "debug" => true,
    ],
    "db" => [
        "host" => "mysql",
        "name" => "phone_store",
        "user" => "phone_store",
        "pass" => "phone_store_password",
    ],
];
EOF

# Copy fixed config.php to Docker container
docker cp tmp/config.php phone-store-php-1:/var/www/html/src/config/

# Restart PHP container
docker restart phone-store-php-1

# Clean up
rm -rf tmp 

echo "Database configuration fix applied successfully." 