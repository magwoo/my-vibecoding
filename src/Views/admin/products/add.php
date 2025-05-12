<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-stone-800">Add Product</h1>
        <div>
            <a href="/admin/products" class="text-stone-600 hover:text-stone-800 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Products
            </a>
        </div>
    </div>
    
    <?php if (isset($errors['general'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= $errors['general'] ?></span>
        </div>
    <?php endif; ?>
    
    <form action="/admin/product/add" method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Product Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-stone-700 mb-1">Product Name</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="<?= isset($product['name']) ? htmlspecialchars($product['name']) : '' ?>"
                    class="w-full border border-stone-300 rounded-md px-3 py-2 focus:ring-stone-500 focus:border-stone-500"
                    required
                >
                <?php if (isset($errors['name'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['name'] ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Brand -->
            <div>
                <label for="brand" class="block text-sm font-medium text-stone-700 mb-1">Brand</label>
                <input 
                    type="text" 
                    id="brand" 
                    name="brand" 
                    value="<?= isset($product['brand']) ? htmlspecialchars($product['brand']) : '' ?>"
                    class="w-full border border-stone-300 rounded-md px-3 py-2 focus:ring-stone-500 focus:border-stone-500"
                    required
                >
                <?php if (isset($errors['brand'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['brand'] ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Price -->
            <div>
                <label for="price" class="block text-sm font-medium text-stone-700 mb-1">Price ($)</label>
                <input 
                    type="number" 
                    id="price" 
                    name="price" 
                    step="0.01" 
                    min="0" 
                    value="<?= isset($product['price']) ? htmlspecialchars($product['price']) : '' ?>"
                    class="w-full border border-stone-300 rounded-md px-3 py-2 focus:ring-stone-500 focus:border-stone-500"
                    required
                >
                <?php if (isset($errors['price'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['price'] ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Screen Size -->
            <div>
                <label for="screen_size" class="block text-sm font-medium text-stone-700 mb-1">Screen Size (inches)</label>
                <input 
                    type="number" 
                    id="screen_size" 
                    name="screen_size" 
                    step="0.1" 
                    min="0" 
                    value="<?= isset($product['screen_size']) ? htmlspecialchars($product['screen_size']) : '' ?>"
                    class="w-full border border-stone-300 rounded-md px-3 py-2 focus:ring-stone-500 focus:border-stone-500"
                    required
                >
                <?php if (isset($errors['screen_size'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['screen_size'] ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Storage -->
            <div>
                <label for="storage" class="block text-sm font-medium text-stone-700 mb-1">Storage</label>
                <input 
                    type="text" 
                    id="storage" 
                    name="storage" 
                    placeholder="e.g. 128GB" 
                    value="<?= isset($product['storage']) ? htmlspecialchars($product['storage']) : '' ?>"
                    class="w-full border border-stone-300 rounded-md px-3 py-2 focus:ring-stone-500 focus:border-stone-500"
                    required
                >
                <?php if (isset($errors['storage'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['storage'] ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Operating System -->
            <div>
                <label for="os" class="block text-sm font-medium text-stone-700 mb-1">Operating System</label>
                <input 
                    type="text" 
                    id="os" 
                    name="os" 
                    placeholder="e.g. iOS, Android" 
                    value="<?= isset($product['os']) ? htmlspecialchars($product['os']) : '' ?>"
                    class="w-full border border-stone-300 rounded-md px-3 py-2 focus:ring-stone-500 focus:border-stone-500"
                    required
                >
                <?php if (isset($errors['os'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['os'] ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Color -->
            <div>
                <label for="color" class="block text-sm font-medium text-stone-700 mb-1">Color</label>
                <input 
                    type="text" 
                    id="color" 
                    name="color" 
                    value="<?= isset($product['color']) ? htmlspecialchars($product['color']) : '' ?>"
                    class="w-full border border-stone-300 rounded-md px-3 py-2 focus:ring-stone-500 focus:border-stone-500"
                    required
                >
                <?php if (isset($errors['color'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['color'] ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Image URL -->
            <div>
                <label for="image_url" class="block text-sm font-medium text-stone-700 mb-1">Image URL</label>
                <input 
                    type="url" 
                    id="image_url" 
                    name="image_url" 
                    placeholder="https://example.com/image.jpg" 
                    value="<?= isset($product['image_url']) ? htmlspecialchars($product['image_url']) : '' ?>"
                    class="w-full border border-stone-300 rounded-md px-3 py-2 focus:ring-stone-500 focus:border-stone-500"
                    required
                >
                <?php if (isset($errors['image_url'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['image_url'] ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Description -->
        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-stone-700 mb-1">Description</label>
            <textarea 
                id="description" 
                name="description" 
                rows="5" 
                class="w-full border border-stone-300 rounded-md px-3 py-2 focus:ring-stone-500 focus:border-stone-500"
                required
            ><?= isset($product['description']) ? htmlspecialchars($product['description']) : '' ?></textarea>
            <?php if (isset($errors['description'])): ?>
                <p class="mt-1 text-sm text-red-600"><?= $errors['description'] ?></p>
            <?php endif; ?>
        </div>
        
        <div class="flex justify-end">
            <a href="/admin/products" class="bg-stone-100 text-stone-700 hover:bg-stone-200 px-4 py-2 rounded-md mr-2">
                Cancel
            </a>
            <button type="submit" class="bg-stone-700 hover:bg-stone-800 text-white px-4 py-2 rounded-md">
                Add Product
            </button>
        </div>
    </form>
</div> 