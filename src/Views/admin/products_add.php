<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-stone-800">Add New Product</h1>
            <a href="/admin/products" class="text-stone-600 hover:text-stone-900">
                <i class="fas fa-arrow-left mr-2"></i> Back to Products
            </a>
        </div>

        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/admin/products/add" method="POST" enctype="multipart/form-data" class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-stone-700">Product Name</label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="<?= htmlspecialchars($name ?? '') ?>"
                       class="mt-1 block w-full border border-stone-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-stone-500 focus:border-stone-500 sm:text-sm"
                       required>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-stone-700">Description</label>
                <textarea name="description" 
                          id="description" 
                          rows="4" 
                          class="mt-1 block w-full border border-stone-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-stone-500 focus:border-stone-500 sm:text-sm"
                          required><?= htmlspecialchars($description ?? '') ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="price" class="block text-sm font-medium text-stone-700">Price</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-stone-500 sm:text-sm">$</span>
                        </div>
                        <input type="number" 
                               name="price" 
                               id="price" 
                               step="0.01" 
                               value="<?= htmlspecialchars($price ?? '') ?>"
                               class="block w-full pl-7 pr-12 border border-stone-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-stone-500 focus:border-stone-500 sm:text-sm"
                               required>
                    </div>
                </div>

                <div>
                    <label for="stock" class="block text-sm font-medium text-stone-700">Stock</label>
                    <input type="number" 
                           name="stock" 
                           id="stock" 
                           value="<?= htmlspecialchars($stock ?? '') ?>"
                           class="mt-1 block w-full border border-stone-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-stone-500 focus:border-stone-500 sm:text-sm"
                           required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="brand" class="block text-sm font-medium text-stone-700">Brand</label>
                    <input type="text" 
                           name="brand" 
                           id="brand" 
                           value="<?= htmlspecialchars($brand ?? '') ?>"
                           class="mt-1 block w-full border border-stone-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-stone-500 focus:border-stone-500 sm:text-sm"
                           required>
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-stone-700">Product Image</label>
                    <input type="file" 
                           name="image" 
                           id="image" 
                           accept="image/*"
                           class="mt-1 block w-full text-sm text-stone-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-md file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-stone-50 file:text-stone-700
                                  hover:file:bg-stone-100"
                           required>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" 
                        class="bg-stone-700 hover:bg-stone-800 text-white px-4 py-2 rounded-md text-sm inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i> Add Product
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
