<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

class HomeController extends Controller
{
    private $productModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
    }
    
    public function index()
    {
        try {
            $featuredProducts = $this->productModel->getFeaturedProducts(8);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $featuredProducts = [];
        }
        
        try {
            $latestProducts = $this->productModel->getProducts([], "created_at DESC", 1, 8);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $latestProducts = [];
        }
        
        try {
            $brands = $this->productModel->getAllBrands();
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $brands = [];
        }
        
        $this->view("home/index", [
            "title" => "Welcome",
            "featuredProducts" => $featuredProducts,
            "latestProducts" => $latestProducts,
            "brands" => $brands,
            "user" => $this->getCurrentUser()
        ]);
    }
} 