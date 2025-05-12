#!/bin/bash

# Create temporary directory
mkdir -p tmp/products

# Create updated index.php view file for products listing
cat > tmp/products/index.php << 'EOF'
<!-- Products page -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <h1 class="text-2xl font-bold text-stone-800 mb-4">All Products</h1>
    
    <div class="flex items-center justify-between mb-4">
        <!-- Show active filters -->
        <div class="flex flex-wrap gap-2">
            <?php if(!empty($filters)): ?>
                <span class="text-sm text-stone-600 mr-2">Active filters:</span>
                
                <?php if(!empty($filters['brand'])): ?>
                    <span class="bg-stone-100 text-stone-800 text-xs py-1 px-2 rounded-full">
                        Brand: <?= $filters['brand'] ?>
                        <a href="<?= removeFilterFromUrl('brand') ?>" class="ml-1 text-stone-500 hover:text-stone-700">×</a>
                    </span>
                <?php endif; ?>
                
                <?php if(isset($filters['min_price'])): ?>
                    <span class="bg-stone-100 text-stone-800 text-xs py-1 px-2 rounded-full">
                        Min Price: $<?= number_format($filters['min_price'], 2) ?>
                        <a href="<?= removeFilterFromUrl('min_price') ?>" class="ml-1 text-stone-500 hover:text-stone-700">×</a>
                    </span>
                <?php endif; ?>
                
                <?php if(isset($filters['max_price'])): ?>
                    <span class="bg-stone-100 text-stone-800 text-xs py-1 px-2 rounded-full">
                        Max Price: $<?= number_format($filters['max_price'], 2) ?>
                        <a href="<?= removeFilterFromUrl('max_price') ?>" class="ml-1 text-stone-500 hover:text-stone-700">×</a>
                    </span>
                <?php endif; ?>
                
                <?php if(isset($filters['screen_size'])): ?>
                    <span class="bg-stone-100 text-stone-800 text-xs py-1 px-2 rounded-full">
                        Screen: <?= $filters['screen_size'] ?>
                        <a href="<?= removeFilterFromUrl('screen_size') ?>" class="ml-1 text-stone-500 hover:text-stone-700">×</a>
                    </span>
                <?php endif; ?>
                
                <?php if(isset($filters['storage'])): ?>
                    <span class="bg-stone-100 text-stone-800 text-xs py-1 px-2 rounded-full">
                        Storage: <?= $filters['storage'] ?>
                        <a href="<?= removeFilterFromUrl('storage') ?>" class="ml-1 text-stone-500 hover:text-stone-700">×</a>
                    </span>
                <?php endif; ?>
                
                <?php if(isset($filters['os'])): ?>
                    <span class="bg-stone-100 text-stone-800 text-xs py-1 px-2 rounded-full">
                        OS: <?= $filters['os'] ?>
                        <a href="<?= removeFilterFromUrl('os') ?>" class="ml-1 text-stone-500 hover:text-stone-700">×</a>
                    </span>
                <?php endif; ?>
                
                <?php if(isset($filters['search'])): ?>
                    <span class="bg-stone-100 text-stone-800 text-xs py-1 px-2 rounded-full">
                        Search: "<?= htmlspecialchars($filters['search']) ?>"
                        <a href="<?= removeFilterFromUrl('search') ?>" class="ml-1 text-stone-500 hover:text-stone-700">×</a>
                    </span>
                <?php endif; ?>
                
                <?php if(!empty($filters)): ?>
                    <a href="/products" class="text-stone-500 hover:text-stone-700 text-xs py-1 px-2 border border-stone-300 rounded-full ml-2">
                        Clear all
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <!-- Sort options -->
        <div class="flex items-center">
            <span class="text-sm text-stone-600 mr-2">Sort by:</span>
            <select id="sort-select" class="border border-stone-300 rounded-md text-stone-700 text-sm focus:outline-none focus:ring-stone-500 focus:border-stone-500">
                <option value="created_at DESC" <?= $sort === 'created_at DESC' ? 'selected' : '' ?>>Newest</option>
                <option value="price ASC" <?= $sort === 'price ASC' ? 'selected' : '' ?>>Price: Low to High</option>
                <option value="price DESC" <?= $sort === 'price DESC' ? 'selected' : '' ?>>Price: High to Low</option>
            </select>
        </div>
    </div>
    
    <!-- Products count -->
    <p class="text-sm text-stone-600 mb-6">Showing <?= count($products) ?> of <?= $totalProducts ?> products</p>
</div>

<div class="flex flex-col md:flex-row gap-6">
    <!-- Filters sidebar -->
    <div class="md:w-1/4 lg:w-1/5">
        <div class="bg-white rounded-lg shadow-sm p-4 sticky top-4">
            <h2 class="text-lg font-semibold text-stone-800 mb-4">Filters</h2>
            
            <form id="filter-form" action="/products" method="GET">
                <!-- Preserve current sort option -->
                <input type="hidden" name="sort" value="<?= $sort ?>">
                
                <!-- Search term (if exists) -->
                <?php if(isset($filters['search'])): ?>
                    <input type="hidden" name="search" value="<?= htmlspecialchars($filters['search']) ?>">
                <?php endif; ?>
                
                <!-- Price Range -->
                <div class="mb-4">
                    <h3 class="text-md font-medium text-stone-800 mb-2">Price Range</h3>
                    <div class="flex space-x-2">
                        <div class="w-1/2">
                            <label class="text-xs text-stone-600">Min</label>
                            <input 
                                type="number" 
                                name="min_price" 
                                class="w-full border border-stone-300 rounded-md px-2 py-1 text-sm"
                                placeholder="$0"
                                value="<?= isset($filters['min_price']) ? $filters['min_price'] : '' ?>"
                                min="0"
                            >
                        </div>
                        <div class="w-1/2">
                            <label class="text-xs text-stone-600">Max</label>
                            <input 
                                type="number" 
                                name="max_price" 
                                class="w-full border border-stone-300 rounded-md px-2 py-1 text-sm"
                                placeholder="$2000"
                                value="<?= isset($filters['max_price']) ? $filters['max_price'] : '' ?>"
                                min="0"
                            >
                        </div>
                    </div>
                </div>
                
                <!-- Brands -->
                <div class="mb-4">
                    <h3 class="text-md font-medium text-stone-800 mb-2">Brand</h3>
                    <div class="space-y-1 max-h-40 overflow-y-auto">
                        <?php foreach($brands as $brand): ?>
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    id="brand-<?= strtolower($brand) ?>" 
                                    name="brand" 
                                    value="<?= $brand ?>" 
                                    class="h-4 w-4 border-stone-300 rounded text-stone-600 focus:ring-stone-500"
                                    <?= isset($filters['brand']) && $filters['brand'] == $brand ? 'checked' : '' ?>
                                >
                                <label for="brand-<?= strtolower($brand) ?>" class="ml-2 text-sm text-stone-700">
                                    <?= $brand ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Screen Size -->
                <div class="mb-4">
                    <h3 class="text-md font-medium text-stone-800 mb-2">Screen Size</h3>
                    <div class="space-y-1 max-h-40 overflow-y-auto">
                        <?php foreach($screenSizes as $size): ?>
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    id="screen-size-<?= str_replace('.', '_', $size) ?>" 
                                    name="screen_size" 
                                    value="<?= $size ?>" 
                                    class="h-4 w-4 border-stone-300 rounded text-stone-600 focus:ring-stone-500"
                                    <?= isset($filters['screen_size']) && $filters['screen_size'] == $size ? 'checked' : '' ?>
                                >
                                <label for="screen-size-<?= str_replace('.', '_', $size) ?>" class="ml-2 text-sm text-stone-700">
                                    <?= $size ?> inches
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Storage -->
                <div class="mb-4">
                    <h3 class="text-md font-medium text-stone-800 mb-2">Storage</h3>
                    <div class="space-y-1 max-h-40 overflow-y-auto">
                        <?php foreach($storageOptions as $option): ?>
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    id="storage-<?= strtolower(str_replace(['GB', ' '], '', $option)) ?>" 
                                    name="storage" 
                                    value="<?= $option ?>" 
                                    class="h-4 w-4 border-stone-300 rounded text-stone-600 focus:ring-stone-500"
                                    <?= isset($filters['storage']) && $filters['storage'] == $option ? 'checked' : '' ?>
                                >
                                <label for="storage-<?= strtolower(str_replace(['GB', ' '], '', $option)) ?>" class="ml-2 text-sm text-stone-700">
                                    <?= $option ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Operating System -->
                <div class="mb-4">
                    <h3 class="text-md font-medium text-stone-800 mb-2">Operating System</h3>
                    <div class="space-y-1 max-h-40 overflow-y-auto">
                        <?php foreach($osOptions as $os): ?>
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    id="os-<?= strtolower($os) ?>" 
                                    name="os" 
                                    value="<?= $os ?>" 
                                    class="h-4 w-4 border-stone-300 rounded text-stone-600 focus:ring-stone-500"
                                    <?= isset($filters['os']) && $filters['os'] == $os ? 'checked' : '' ?>
                                >
                                <label for="os-<?= strtolower($os) ?>" class="ml-2 text-sm text-stone-700">
                                    <?= $os ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-stone-700 hover:bg-stone-800 text-white py-2 px-4 rounded-md transition">
                    Apply Filters
                </button>
            </form>
        </div>
    </div>
    
    <!-- Products grid -->
    <div class="md:w-3/4 lg:w-4/5">
        <?php if(empty($products)): ?>
            <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                <i class="fas fa-search text-4xl text-stone-400 mb-4"></i>
                <h2 class="text-lg font-semibold text-stone-800 mb-2">No products found</h2>
                <p class="text-stone-600 mb-4">Try adjusting your filters or search term.</p>
                <a href="/products" class="inline-block bg-stone-700 hover:bg-stone-800 text-white py-2 px-4 rounded-md transition">
                    Clear Filters
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($products as $product): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <a href="/product/<?= $product['id'] ?>">
                            <img src="<?= $product['image_url'] ?>" alt="<?= $product['name'] ?>" class="w-full h-48 object-cover">
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-stone-800 mb-1"><?= $product['name'] ?></h3>
                                <p class="text-stone-600 text-sm mb-3">
                                    <?= $product['brand'] ?> • <?= $product['storage'] ?> • <?= $product['os'] ?>
                                </p>
                                <div class="flex items-center justify-between">
                                    <span class="text-xl font-bold text-stone-800">$<?= number_format($product['price'], 2) ?></span>
                                    <form action="/cart/add" method="POST" class="inline">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <button type="submit" class="bg-stone-100 hover:bg-stone-200 text-stone-800 p-2 rounded-full transition">
                                            <i class="fas fa-shopping-cart"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if($totalPages > 1): ?>
                <div class="flex justify-center mt-8">
                    <div class="flex space-x-1">
                        <?php if($currentPage > 1): ?>
                            <a href="<?= paginationUrl($currentPage - 1) ?>" class="px-4 py-2 bg-white border border-stone-300 rounded-md text-stone-700 hover:bg-stone-50">
                                Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php for($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                            <a 
                                href="<?= paginationUrl($i) ?>" 
                                class="px-4 py-2 <?= $i === $currentPage ? 'bg-stone-800 text-white' : 'bg-white text-stone-700 hover:bg-stone-50' ?> border border-stone-300 rounded-md"
                            >
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if($currentPage < $totalPages): ?>
                            <a href="<?= paginationUrl($currentPage + 1) ?>" class="px-4 py-2 bg-white border border-stone-300 rounded-md text-stone-700 hover:bg-stone-50">
                                Next
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php
// Helper function to remove a filter from the current URL
function removeFilterFromUrl($param, $value = null) {
    $params = $_GET;
    
    if ($value !== null && isset($params[$param]) && is_array($params[$param])) {
        $key = array_search($value, $params[$param]);
        if ($key !== false) {
            unset($params[$param][$key]);
        }
        if (empty($params[$param])) {
            unset($params[$param]);
        }
    } else {
        unset($params[$param]);
    }
    
    $queryString = http_build_query($params);
    return '/products' . ($queryString ? '?' . $queryString : '');
}

// Helper function to generate pagination URLs
function paginationUrl($page) {
    $params = $_GET;
    $params['page'] = $page;
    $queryString = http_build_query($params);
    return '/products?' . $queryString;
}
?>

<script>
    // Update sort order via JavaScript
    document.getElementById('sort-select').addEventListener('change', function() {
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('sort', this.value);
        window.location.href = currentUrl.toString();
    });
</script>
EOF

# Create updated show.php view file for product details
cat > tmp/products/show.php << 'EOF'
<!-- Product detail page -->
<div class="container mx-auto px-4">
    <div class="mb-6">
        <a href="/products" class="text-stone-600 hover:text-stone-800 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Products
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="flex flex-col md:flex-row">
            <!-- Product Image -->
            <div class="md:w-1/2">
                <img src="<?= $product['image_url'] ?>" alt="<?= $product['name'] ?>" class="w-full h-auto object-cover">
            </div>
            
            <!-- Product Details -->
            <div class="md:w-1/2 p-6">
                <h1 class="text-3xl font-bold text-stone-800 mb-2"><?= $product['name'] ?></h1>
                <p class="text-stone-600 mb-4"><?= $product['brand'] ?></p>
                
                <div class="flex items-center mb-4">
                    <span class="text-3xl font-bold text-stone-800"><?= '$' . number_format($product['price'], 2) ?></span>
                    <?php if($product['featured']): ?>
                        <span class="ml-4 bg-stone-700 text-white text-xs px-2 py-1 rounded">Featured</span>
                    <?php endif; ?>
                </div>
                
                <div class="border-t border-b border-stone-200 py-4 my-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-stone-600 text-sm">OS:</span>
                            <p class="font-medium"><?= $product['os'] ?></p>
                        </div>
                        <div>
                            <span class="text-stone-600 text-sm">Screen Size:</span>
                            <p class="font-medium"><?= $product['screen_size'] ?> inches</p>
                        </div>
                        <div>
                            <span class="text-stone-600 text-sm">Storage:</span>
                            <p class="font-medium"><?= $product['storage'] ?></p>
                        </div>
                        <div>
                            <span class="text-stone-600 text-sm">Color:</span>
                            <p class="font-medium"><?= $product['color'] ?></p>
                        </div>
                    </div>
                </div>
                
                <p class="text-stone-700 mb-6">
                    <?= $product['description'] ?>
                </p>
                
                <form action="/cart/add" method="POST" class="mb-4">
                    <div class="flex items-center mb-4">
                        <label for="quantity" class="mr-2">Quantity:</label>
                        <select id="quantity" name="quantity" class="border border-stone-300 rounded-md px-2 py-1">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <button type="submit" class="w-full bg-stone-700 hover:bg-stone-800 text-white py-3 px-6 rounded-md flex items-center justify-center transition">
                        <i class="fas fa-shopping-cart mr-2"></i> Add to Cart
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if(!empty($relatedProducts)): ?>
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-stone-800 mb-6">Related Products</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach($relatedProducts as $relatedProduct): ?>
                    <?php if($relatedProduct['id'] != $product['id']): ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                            <a href="/product/<?= $relatedProduct['id'] ?>">
                                <img src="<?= $relatedProduct['image_url'] ?>" alt="<?= $relatedProduct['name'] ?>" class="w-full h-48 object-cover">
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold text-stone-800 mb-1"><?= $relatedProduct['name'] ?></h3>
                                    <p class="text-stone-600 text-sm mb-3"><?= $relatedProduct['brand'] ?></p>
                                    <span class="text-xl font-bold text-stone-800">$<?= number_format($relatedProduct['price'], 2) ?></span>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
EOF

# Copy the updated files to the container
docker cp tmp/products/index.php phone-store-php-1:/var/www/html/src/Views/products/
docker cp tmp/products/show.php phone-store-php-1:/var/www/html/src/Views/products/

# Restart PHP container
docker restart phone-store-php-1

# Clean up
rm -rf tmp

echo "Product view files fixed successfully." 