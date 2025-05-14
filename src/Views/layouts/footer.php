    </main>
    
    <!-- Футер -->
    <footer class="bg-stone-800 text-white py-8 mt-auto">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between">
                <!-- Информация о магазине -->
                <div class="mb-6 md:mb-0">
                    <h3 class="text-xl font-bold mb-3">ТелефонМаркет</h3>
                    <p class="text-stone-300">Интернет-магазин современных телефонов и смартфонов.</p>
                </div>
                
                <!-- Навигация -->
                <div class="mb-6 md:mb-0">
                    <h4 class="text-lg font-semibold mb-3">Навигация</h4>
                    <ul class="space-y-2">
                        <li><a href="/" class="text-stone-300 hover:text-white">Главная</a></li>
                        <li><a href="/catalog" class="text-stone-300 hover:text-white">Каталог</a></li>
                        <li><a href="/cart" class="text-stone-300 hover:text-white">Корзина</a></li>
                    </ul>
                </div>
                
                <!-- Аккаунт -->
                <div class="mb-6 md:mb-0">
                    <h4 class="text-lg font-semibold mb-3">Аккаунт</h4>
                    <ul class="space-y-2">
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="/account" class="text-stone-300 hover:text-white">Личный кабинет</a></li>
                        <li><a href="/account/orders" class="text-stone-300 hover:text-white">Мои заказы</a></li>
                        <li><a href="/logout" class="text-stone-300 hover:text-white">Выйти</a></li>
                        <?php else: ?>
                        <li><a href="/login" class="text-stone-300 hover:text-white">Вход</a></li>
                        <li><a href="/register" class="text-stone-300 hover:text-white">Регистрация</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <!-- Контакты -->
                <div>
                    <h4 class="text-lg font-semibold mb-3">Контакты</h4>
                    <ul class="space-y-2">
                        <li class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="text-stone-300">info@phonemarket.ru</span>
                        </li>
                        <li class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span class="text-stone-300">8 (800) 123-45-67</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Копирайт -->
            <div class="mt-8 pt-4 border-t border-stone-700 text-center text-stone-400">
                <p>&copy; <?= date('Y') ?> ТелефонМаркет. Все права защищены.</p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script>
        // Функция для обновления количества товаров в корзине
        function updateCartCount(count) {
            const cartCountElement = document.querySelector('.cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = count;
            }
        }
        
        // Функция для добавления товара в корзину
        function addToCart(productId, quantity = 1) {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);
            formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?? '' ?>');
            
            fetch('/api/cart/add', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Обновляем счетчик товаров в корзине
                    updateCartCount(data.cart_count);
                    
                    // Показываем уведомление
                    alert(data.message);
                } else {
                    // Показываем сообщение об ошибке
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при добавлении товара в корзину');
            });
        }
        
        // Функция для обновления количества товара в корзине
        function updateCartItem(productId, quantity) {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);
            formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?? '' ?>');
            
            fetch('/api/cart/update', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Обновляем отображение корзины
                    updateCartDisplay(data);
                } else {
                    // Показываем сообщение об ошибке
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при обновлении корзины');
            });
        }
        
        // Функция для удаления товара из корзины
        function removeFromCart(productId) {
            if (confirm('Вы уверены, что хотите удалить этот товар из корзины?')) {
                const formData = new FormData();
                formData.append('product_id', productId);
                formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?? '' ?>');
                
                fetch('/api/cart/remove', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Обновляем отображение корзины
                        updateCartDisplay(data);
                    } else {
                        // Показываем сообщение об ошибке
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Произошла ошибка при удалении товара из корзины');
                });
            }
        }
        
        // Функция для обновления отображения корзины
        function updateCartDisplay(data) {
            // Обновляем счетчик товаров в корзине
            updateCartCount(data.cart_count);
            
            // Обновляем общую стоимость
            const totalElement = document.querySelector('.cart-total');
            if (totalElement) {
                totalElement.textContent = data.total.toFixed(2) + ' ₽';
            }
            
            // Обновляем элементы корзины
            const cartItemsContainer = document.querySelector('.cart-items');
            if (cartItemsContainer && data.items) {
                // Если корзина пуста, показываем сообщение
                if (data.items.length === 0) {
                    cartItemsContainer.innerHTML = '<div class="text-center py-8"><p class="text-gray-500">Ваша корзина пуста</p></div>';
                    
                    // Скрываем кнопку оформления заказа
                    const checkoutButton = document.querySelector('.checkout-button');
                    if (checkoutButton) {
                        checkoutButton.style.display = 'none';
                    }
                } else {
                    // Обновляем каждый элемент в корзине
                    // Примечание: для полного обновления нужно перезагрузить страницу или 
                    // динамически обновить DOM на основе полученных данных
                    
                    // Показываем кнопку оформления заказа
                    const checkoutButton = document.querySelector('.checkout-button');
                    if (checkoutButton) {
                        checkoutButton.style.display = 'block';
                    }
                }
            }
        }
    </script>
</body>
</html>
