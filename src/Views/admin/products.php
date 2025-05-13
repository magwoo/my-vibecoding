<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-stone-800">Products</h1>
            <a href="/admin/products/add" class="bg-stone-700 hover:bg-stone-800 text-white px-4 py-2 rounded-md text-sm inline-flex items-center">
                <i class="fas fa-plus mr-2"></i> Add Product
            </a>
        </div>

        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <?php if (empty($products)): ?>
            <div class="text-center py-8">
                <p class="text-stone-500">No products found</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-stone-50 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Image</th>
                            <th class="px-6 py-3 bg-stone-50 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 bg-stone-50 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Brand</th>
                            <th class="px-6 py-3 bg-stone-50 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 bg-stone-50 text-left text-xs font-medium text-stone-500 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 bg-stone-50 text-right text-xs font-medium text-stone-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-stone-200">
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <img src="<?= htmlspecialchars($product['image_url']) ?>" 
                                         alt="<?= htmlspecialchars($product['name']) ?>" 
                                         class="h-12 w-12 object-cover rounded-lg">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-stone-900"><?= htmlspecialchars($product['name']) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-stone-500"><?= htmlspecialchars($product['brand']) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-stone-500">$<?= number_format($product['price'], 2) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-stone-500"><?= $product['stock'] ?></div>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <a href="/admin/products/edit/<?= $product['id'] ?>" 
                                       class="text-stone-600 hover:text-stone-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="deleteProduct(<?= $product['id'] ?>)" 
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="flex justify-center mt-6">
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <?php if ($currentPage > 1): ?>
                            <a href="?page=<?= $currentPage - 1 ?>" 
                               class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-stone-300 bg-white text-sm font-medium text-stone-500 hover:bg-stone-50">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i ?>" 
                               class="relative inline-flex items-center px-4 py-2 border border-stone-300 bg-white text-sm font-medium <?= $i === $currentPage ? 'text-stone-900 bg-stone-100' : 'text-stone-500 hover:bg-stone-50' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <a href="?page=<?= $currentPage + 1 ?>" 
                               class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-stone-300 bg-white text-sm font-medium text-stone-500 hover:bg-stone-50">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product?')) {
        fetch(`/admin/products/delete/${productId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error deleting product');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting product');
        });
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
