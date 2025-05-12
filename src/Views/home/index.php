<!-- Hero section -->
<section class="bg-stone-100 py-12 rounded-lg shadow-sm mb-12">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="md:w-1/2">
                <h1 class="text-4xl font-bold text-stone-800 mb-4">Premium Phones at Unbeatable Prices</h1>
                <p class="text-lg text-stone-600 mb-6">
                    Discover the latest smartphones from top brands. Get free shipping and easy returns on all orders.
                </p>
                <a href="/products" class="bg-stone-700 hover:bg-stone-800 text-white px-6 py-3 rounded-md inline-block transition">
                    Shop Now
                </a>
            </div>
            <div class="md:w-1/2">
                <img src="https://i.imgur.com/fHyEMsl.jpg" alt="Latest phones" class="rounded-lg shadow-md w-full">
            </div>
        </div>
    </div>
</section>

<!-- Featured products section -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-stone-800">Featured Products</h2>
            <a href="/products" class="text-stone-600 hover:text-stone-800 transition">View All</a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php if (empty($featuredProducts)): ?>
                <div class="col-span-full text-center py-8">
                    <p class="text-stone-600">No featured products available at the moment. Check back soon!</p>
                </div>
            <?php else: ?>
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <a href="/product/<?= $product['id'] ?>">
                            <img src="<?= $product['image_url'] ?>" alt="<?= $product['name'] ?>" class="w-full h-48 object-cover">
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-stone-800 mb-2"><?= $product['name'] ?></h3>
                                <p class="text-stone-600 text-sm mb-3"><?= $product['brand'] ?></p>
                                <div class="flex items-center justify-between">
                                    <span class="text-xl font-bold text-stone-800">$<?= number_format($product['price'], 2) ?></span>
                                    <span class="text-xs bg-stone-100 text-stone-800 px-2 py-1 rounded-full"><?= $product['storage'] ?></span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Brand categories -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold text-stone-800 mb-6">Shop by Brand</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="/products?brand=Apple" class="bg-white rounded-lg shadow-md overflow-hidden flex items-center justify-center p-6 hover:shadow-lg transition">
                <div class="text-center">
                    <i class="fab fa-apple text-5xl mb-2 text-stone-800"></i>
                    <h3 class="text-lg font-semibold text-stone-800">Apple</h3>
                </div>
            </a>
            
            <a href="/products?brand=Samsung" class="bg-white rounded-lg shadow-md overflow-hidden flex items-center justify-center p-6 hover:shadow-lg transition">
                <div class="text-center">
                    <i class="fas fa-mobile-alt text-5xl mb-2 text-stone-800"></i>
                    <h3 class="text-lg font-semibold text-stone-800">Samsung</h3>
                </div>
            </a>
            
            <a href="/products?brand=Xiaomi" class="bg-white rounded-lg shadow-md overflow-hidden flex items-center justify-center p-6 hover:shadow-lg transition">
                <div class="text-center">
                    <i class="fas fa-mobile text-5xl mb-2 text-stone-800"></i>
                    <h3 class="text-lg font-semibold text-stone-800">Xiaomi</h3>
                </div>
            </a>
            
            <a href="/products?brand=Google" class="bg-white rounded-lg shadow-md overflow-hidden flex items-center justify-center p-6 hover:shadow-lg transition">
                <div class="text-center">
                    <i class="fab fa-google text-5xl mb-2 text-stone-800"></i>
                    <h3 class="text-lg font-semibold text-stone-800">Google</h3>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- Latest products section -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-stone-800">Latest Products</h2>
            <a href="/products?sort=newest" class="text-stone-600 hover:text-stone-800 transition">View All</a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php if (empty($latestProducts)): ?>
                <div class="col-span-full text-center py-8">
                    <p class="text-stone-600">No products available at the moment. Check back soon!</p>
                </div>
            <?php else: ?>
                <?php foreach ($latestProducts as $product): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <a href="/product/<?= $product['id'] ?>">
                            <img src="<?= $product['image_url'] ?>" alt="<?= $product['name'] ?>" class="w-full h-48 object-cover">
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-stone-800 mb-1"><?= $product['name'] ?></h3>
                                <p class="text-stone-600 text-sm mb-3"><?= $product['brand'] ?> • <?= $product['storage'] ?> • <?= $product['color'] ?></p>
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
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Features section -->
<section class="mb-12 bg-stone-100 py-12 rounded-lg">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold text-stone-800 text-center mb-8">Why Choose Us</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <div class="text-stone-700 text-4xl mb-4">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <h3 class="text-xl font-semibold text-stone-800 mb-2">Fast Shipping</h3>
                <p class="text-stone-600">Free shipping on all orders over $50. Get your phone delivered within 2-3 business days.</p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <div class="text-stone-700 text-4xl mb-4">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="text-xl font-semibold text-stone-800 mb-2">Secure Payments</h3>
                <p class="text-stone-600">Your payment information is processed securely. We do not store credit card details.</p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <div class="text-stone-700 text-4xl mb-4">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <h3 class="text-xl font-semibold text-stone-800 mb-2">Easy Returns</h3>
                <p class="text-stone-600">Change your mind? Return the product within 30 days for a full refund.</p>
            </div>
        </div>
    </div>
</section> 