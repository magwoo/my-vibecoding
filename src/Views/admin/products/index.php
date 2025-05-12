<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-stone-800">Products</h1>
        <div>
            <a href="/admin/product/add" class="bg-stone-700 hover:bg-stone-800 text-white px-4 py-2 rounded-md text-sm inline-flex items-center">
                <i class="fas fa-plus mr-2"></i> Add Product
            </a>
        </div>
    </div>
    
    <?php if (empty($products)): ?>
        <div class="bg-stone-50 p-8 rounded-md text-stone-600 text-center">
            <i class="fas fa-box text-3xl text-stone-400 mb-3"></i>
            <p class="text-lg mb-4">No products found</p>
            <a href="/admin/product/add" class="bg-stone-700 hover:bg-stone-800 text-white px-4 py-2 rounded-md text-sm inline-flex items-center">
                <i class="fas fa-plus mr-2"></i> Add Product
            </a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-stone-50 border-b border-stone-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-stone-600">Image</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-stone-600">Name</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-stone-600">Brand</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-stone-600">Storage</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-stone-600">Price</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-stone-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td class="px-4 py-3">
                                <img src="<?= $product['image_url'] ?>" alt="<?= $product['name'] ?>" class="w-12 h-12 object-cover rounded-md">
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-stone-800">
                                <?= $product['name'] ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-stone-600">
                                <?= $product['brand'] ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-stone-600 text-center">
                                <?= $product['storage'] ?>
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-bold text-stone-800">
                                $<?= number_format($product['price'], 2) ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center space-x-3">
                                    <a href="/admin/product/edit?id=<?= $product['id'] ?>" class="text-stone-600 hover:text-stone-800">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="/admin/product/delete" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                        <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if($totalPages > 1): ?>
            <div class="flex justify-center mt-8">
                <div class="flex space-x-1">
                    <?php for($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="/admin/products?page=<?= $i ?>" class="px-3 py-1 rounded-md <?= $i === $page ? 'bg-stone-700 text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div> 