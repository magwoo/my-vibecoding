#!/bin/bash

# Create a simple ProductController for testing
mkdir -p tmp
echo '<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    private $productModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
    }
    
    public function index()
    {
        echo "ProductController index() called<br>";
        phpinfo();
    }
    
    public function show($id = null)
    {
        echo "ProductController show() called<br>";
    }
    
    public function search()
    {
        echo "ProductController search() called<br>";
    }
}' > tmp/ProductController.php

# Copy the controller to the container
docker cp tmp/ProductController.php phone-store-php-1:/var/www/html/src/Controllers/

# Restart containers
docker restart phone-store-php-1

# Clean up
rm -rf tmp 