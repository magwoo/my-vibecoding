<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Router;
use App\Models\User;
use App\Models\Order;

class UserController extends Controller
{
    private $userModel;
    private $orderModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->orderModel = new Order();
        
        // Require login for all actions in this controller
        $this->requireLogin();
    }
    
    public function account()
    {
        $user = $this->getCurrentUser();
        $errors = [];
        
        // Handle profile update
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Profile update form submitted
            if (isset($_POST["update_profile"])) {
                $name = trim($_POST["name"] ?? "");
                $phone = trim($_POST["phone"] ?? "");
                $address = trim($_POST["address"] ?? "");
                
                // Basic validation
                $rules = [
                    "name" => "required",
                    "phone" => "required",
                    "address" => "required"
                ];
                
                $errors = $this->validateInput($_POST, $rules);
                
                if (empty($errors)) {
                    // Update profile
                    $data = [
                        "name" => $name,
                        "phone" => $phone,
                        "address" => $address
                    ];
                    
                    if ($this->userModel->updateProfile($_SESSION["user_id"], $data)) {
                        $_SESSION["success"] = "Profile updated successfully";
                        Router::redirect("account");
                        return;
                    } else {
                        $_SESSION["error"] = "Failed to update profile";
                        Router::redirect("account");
                        return;
                    }
                }
            }
            
            // Password change form submitted
            if (isset($_POST["change_password"])) {
                $currentPassword = $_POST["current_password"] ?? "";
                $newPassword = $_POST["new_password"] ?? "";
                $confirmPassword = $_POST["confirm_password"] ?? "";
                
                // Basic validation
                if (empty($currentPassword)) {
                    $errors["current_password"] = "Current password is required";
                }
                
                if (empty($newPassword)) {
                    $errors["new_password"] = "New password is required";
                } elseif (strlen($newPassword) < 8) {
                    $errors["new_password"] = "Password must be at least 8 characters";
                }
                
                if ($newPassword !== $confirmPassword) {
                    $errors["confirm_password"] = "Password confirmation does not match";
                }
                
                // Verify current password
                if (empty($errors) && !$this->userModel->verifyPassword($user, $currentPassword)) {
                    $errors["current_password"] = "Current password is incorrect";
                }
                
                if (empty($errors)) {
                    // Change password
                    if ($this->userModel->changePassword($_SESSION["user_id"], $newPassword)) {
                        $_SESSION["success"] = "Password changed successfully";
                        Router::redirect("account");
                        return;
                    } else {
                        $_SESSION["error"] = "Failed to change password";
                        Router::redirect("account");
                        return;
                    }
                }
            }
        }
        
        // Display account page
        $this->view("user/account", [
            "title" => "My Account",
            "user" => $user,
            "errors" => $errors
        ]);
    }
    
    public function orders()
    {
        $user = $this->getCurrentUser();
        
        // Get user orders
        $orders = $this->orderModel->getUserOrders($_SESSION["user_id"]);
        
        // Display orders page
        $this->view("user/orders", [
            "title" => "My Orders",
            "user" => $user,
            "orders" => $orders
        ]);
    }
}
