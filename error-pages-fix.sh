#!/bin/bash

# Create temporary directory
mkdir -p tmp/errors

# Create 404 error page
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <div class="flex-grow flex items-center justify-center">
        <div class="max-w-md mx-auto p-6 text-center">
            <div class="mb-6">
                <i class="fas fa-exclamation-circle text-stone-400 text-6xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-stone-800 mb-4">404 - Page Not Found</h1>
            <p class="text-stone-600 mb-8">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
            <a href="/" class="bg-stone-700 hover:bg-stone-800 text-white py-2 px-6 rounded-md transition">
                Return to Home
            </a>
        </div>
    </div>
</body>
</html>' > tmp/errors/404.php

# Create 500 error page
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <div class="flex-grow flex items-center justify-center">
        <div class="max-w-md mx-auto p-6 text-center">
            <div class="mb-6">
                <i class="fas fa-exclamation-triangle text-orange-400 text-6xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-stone-800 mb-4">500 - Server Error</h1>
            <p class="text-stone-600 mb-8">Sorry, something went wrong on our server. We are working to fix the problem.</p>
            <a href="/" class="bg-stone-700 hover:bg-stone-800 text-white py-2 px-6 rounded-md transition">
                Return to Home
            </a>
        </div>
    </div>
</body>
</html>' > tmp/errors/500.php

# Create 403 error page
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <div class="flex-grow flex items-center justify-center">
        <div class="max-w-md mx-auto p-6 text-center">
            <div class="mb-6">
                <i class="fas fa-lock text-red-400 text-6xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-stone-800 mb-4">403 - Access Forbidden</h1>
            <p class="text-stone-600 mb-8">Sorry, you do not have permission to access this page.</p>
            <a href="/" class="bg-stone-700 hover:bg-stone-800 text-white py-2 px-6 rounded-md transition">
                Return to Home
            </a>
        </div>
    </div>
</body>
</html>' > tmp/errors/403.php

# Create errors directory in Docker container
docker exec phone-store-php-1 mkdir -p /var/www/html/public/errors

# Copy error pages to Docker container
docker cp tmp/errors/404.php phone-store-php-1:/var/www/html/public/errors/
docker cp tmp/errors/500.php phone-store-php-1:/var/www/html/public/errors/
docker cp tmp/errors/403.php phone-store-php-1:/var/www/html/public/errors/

# Restart PHP container
docker restart phone-store-php-1

# Clean up
rm -rf tmp 