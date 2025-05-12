<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h1 class="text-2xl font-bold text-stone-800 mb-6">My Account</h1>
        
        <?php if ($updated): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">Your account information has been updated successfully.</span>
            </div>
        <?php endif; ?>
        
        <form action="/account" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-stone-800 mb-3">Account Information</h2>
                
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-stone-700 mb-1">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?= htmlspecialchars($user['email']) ?>"
                        class="w-full border border-stone-300 rounded-md px-3 py-2 focus:ring-stone-500 focus:border-stone-500"
                        required
                    >
                    <?php if (isset($errors['email'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= $errors['email'] ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-stone-800 mb-3">Change Password</h2>
                <p class="text-stone-600 mb-4 text-sm">Leave these fields empty if you don't want to change your password.</p>
                
                <div class="mb-4">
                    <label for="current_password" class="block text-sm font-medium text-stone-700 mb-1">Current Password</label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password" 
                        class="w-full border border-stone-300 rounded-md px-3 py-2 focus:ring-stone-500 focus:border-stone-500"
                    >
                    <?php if (isset($errors['current_password'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= $errors['current_password'] ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="mb-4">
                    <label for="new_password" class="block text-sm font-medium text-stone-700 mb-1">New Password</label>
                    <input 
                        type="password" 
                        id="new_password" 
                        name="new_password" 
                        class="w-full border border-stone-300 rounded-md px-3 py-2 focus:ring-stone-500 focus:border-stone-500"
                    >
                    <p class="mt-1 text-xs text-stone-500">Must be at least 8 characters</p>
                    <?php if (isset($errors['new_password'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= $errors['new_password'] ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="mb-4">
                    <label for="confirm_password" class="block text-sm font-medium text-stone-700 mb-1">Confirm New Password</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        class="w-full border border-stone-300 rounded-md px-3 py-2 focus:ring-stone-500 focus:border-stone-500"
                    >
                    <?php if (isset($errors['confirm_password'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= $errors['confirm_password'] ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-stone-700 hover:bg-stone-800 text-white px-4 py-2 rounded-md">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-stone-800 mb-3">Account Actions</h2>
        
        <div class="flex items-center justify-between p-4 border border-stone-200 rounded-md">
            <div>
                <h3 class="font-medium text-stone-800">My Orders</h3>
                <p class="text-stone-600 text-sm">View your order history</p>
            </div>
            <a href="/orders" class="bg-stone-100 hover:bg-stone-200 text-stone-800 px-4 py-2 rounded-md transition">
                View Orders
            </a>
        </div>
        
        <div class="flex items-center justify-between p-4 border border-stone-200 rounded-md mt-4">
            <div>
                <h3 class="font-medium text-stone-800">Log Out</h3>
                <p class="text-stone-600 text-sm">Sign out of your account</p>
            </div>
            <a href="/logout" class="bg-red-100 hover:bg-red-200 text-red-800 px-4 py-2 rounded-md transition">
                Log Out
            </a>
        </div>
    </div>
</div> 