#!/bin/bash

# Create temporary directory
mkdir -p tmp/auth

# Create login view with CSRF token
echo '<!-- Login Page -->
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h1 class="text-2xl font-bold text-stone-800 mb-4">Login to Your Account</h1>
        
        <?php if (isset($errors["login"])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= $errors["login"] ?>
            </div>
        <?php endif; ?>
        
        <form action="/login" method="post">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION["csrf_token"] ?>">
            
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-stone-700 mb-1">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?= htmlspecialchars($email ?? "") ?>"
                    class="w-full border border-stone-300 rounded-md px-3 py-2 focus:ring-stone-500 focus:border-stone-500"
                    required
                >
                <?php if (isset($errors["email"])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors["email"] ?></p>
                <?php endif; ?>
            </div>
            
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-stone-700 mb-1">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="w-full border border-stone-300 rounded-md px-3 py-2 focus:ring-stone-500 focus:border-stone-500"
                    required
                >
                <?php if (isset($errors["password"])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors["password"] ?></p>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="w-full bg-stone-700 hover:bg-stone-800 text-white py-2 px-4 rounded-md transition">
                Login
            </button>
        </form>
        
        <div class="mt-4 text-center">
            <p class="text-stone-600">
                Don'\''t have an account? <a href="/register" class="text-stone-800 hover:underline">Register here</a>
            </p>
        </div>
    </div>
</div>' > tmp/auth/login.php

# Create register view with CSRF token
echo '<!-- Registration Page -->
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h1 class="text-2xl font-bold text-stone-800 mb-4">Create an Account</h1>
        
        <?php if (isset($errors["register"])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= $errors["register"] ?>
            </div>
        <?php endif; ?>
        
        <form action="/register" method="post">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION["csrf_token"] ?>">
            
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-stone-700 mb-1">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?= htmlspecialchars($email ?? "") ?>"
                    class="w-full border border-stone-300 rounded-md px-3 py-2 focus:ring-stone-500 focus:border-stone-500"
                    required
                >
                <?php if (isset($errors["email"])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors["email"] ?></p>
                <?php endif; ?>
            </div>
            
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-stone-700 mb-1">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="w-full border border-stone-300 rounded-md px-3 py-2 focus:ring-stone-500 focus:border-stone-500"
                    required
                >
                <p class="mt-1 text-xs text-stone-500">Must be at least 8 characters</p>
                <?php if (isset($errors["password"])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors["password"] ?></p>
                <?php endif; ?>
            </div>
            
            <div class="mb-6">
                <label for="confirm_password" class="block text-sm font-medium text-stone-700 mb-1">Confirm Password</label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    class="w-full border border-stone-300 rounded-md px-3 py-2 focus:ring-stone-500 focus:border-stone-500"
                    required
                >
                <?php if (isset($errors["confirm_password"])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors["confirm_password"] ?></p>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="w-full bg-stone-700 hover:bg-stone-800 text-white py-2 px-4 rounded-md transition">
                Register
            </button>
        </form>
        
        <div class="mt-4 text-center">
            <p class="text-stone-600">
                Already have an account? <a href="/login" class="text-stone-800 hover:underline">Login here</a>
            </p>
        </div>
    </div>
</div>' > tmp/auth/register.php

# Copy updated auth views to Docker container
docker cp tmp/auth/login.php phone-store-php-1:/var/www/html/src/Views/auth/
docker cp tmp/auth/register.php phone-store-php-1:/var/www/html/src/Views/auth/

# Restart PHP container
docker restart phone-store-php-1

# Clean up
rm -rf tmp 