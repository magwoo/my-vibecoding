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
