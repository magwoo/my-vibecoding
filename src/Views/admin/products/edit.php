<!-- Заголовок страницы -->
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Редактирование товара</h1>
    <a href="/admin/products" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md transition flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Назад к списку
    </a>
</div>

<!-- Форма редактирования товара -->
<div class="bg-white rounded-lg shadow-md p-6">
    <form action="/admin/products/update/<?= $product['id'] ?>" method="POST" enctype="multipart/form-data">
        <!-- CSRF токен -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Левая колонка -->
            <div>
                <!-- Название товара -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Название товара*</label>
                    <input type="text" id="name" name="name" required value="<?= htmlspecialchars($product['name']) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500">
                </div>
                
                <!-- Бренд -->
                <div class="mb-4">
                    <label for="brand" class="block text-sm font-medium text-gray-700 mb-1">Бренд*</label>
                    <select id="brand" name="brand" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500">
                        <option value="">Выберите бренд</option>
                        <?php 
                        $brandFound = false;
                        foreach ($brands as $brand): 
                            $selected = $brand === $product['brand'] ? 'selected' : '';
                            if ($selected) $brandFound = true;
                        ?>
                        <option value="<?= $brand ?>" <?= $selected ?>><?= $brand ?></option>
                        <?php endforeach; ?>
                        <option value="other" <?= !$brandFound ? 'selected' : '' ?>>Другой (укажите ниже)</option>
                    </select>
                    <!-- Поле для ввода нового бренда (скрыто по умолчанию) -->
                    <div id="new-brand-container" class="mt-2 <?= !$brandFound ? '' : 'hidden' ?>">
                        <input type="text" id="new_brand" name="new_brand" placeholder="Введите название бренда"
                               value="<?= !$brandFound ? htmlspecialchars($product['brand']) : '' ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500">
                    </div>
                </div>
                
                <!-- Цена -->
                <div class="mb-4">
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Цена (₽)*</label>
                    <input type="number" id="price" name="price" min="0" step="0.01" required value="<?= $product['price'] ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500">
                </div>
                
                <!-- Изображение -->
                <div class="mb-4">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Изображение</label>
                    <?php if (!empty($product['image_path'])): ?>
                    <div class="mb-2">
                        <img src="<?= $product['image_path'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="h-32 object-contain">
                        <p class="text-xs text-gray-500 mt-1">Текущее изображение. Загрузите новое, чтобы заменить.</p>
                    </div>
                    <?php endif; ?>
                    <div class="flex items-center">
                        <input type="file" id="image" name="image" accept="image/*"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Рекомендуемый размер: 600x600 пикселей</p>
                </div>
            </div>
            
            <!-- Правая колонка (характеристики) -->
            <div>
                <h3 class="text-lg font-semibold mb-3">Характеристики</h3>
                
                <!-- Оперативная память -->
                <div class="mb-4">
                    <label for="ram" class="block text-sm font-medium text-gray-700 mb-1">Оперативная память</label>
                    <input type="text" id="ram" name="ram" placeholder="Например: 8 ГБ"
                           value="<?= htmlspecialchars($product['specs']['ram'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500">
                </div>
                
                <!-- Встроенная память -->
                <div class="mb-4">
                    <label for="storage" class="block text-sm font-medium text-gray-700 mb-1">Встроенная память</label>
                    <input type="text" id="storage" name="storage" placeholder="Например: 128 ГБ"
                           value="<?= htmlspecialchars($product['specs']['storage'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500">
                </div>
                
                <!-- Экран -->
                <div class="mb-4">
                    <label for="screen" class="block text-sm font-medium text-gray-700 mb-1">Диагональ экрана</label>
                    <input type="text" id="screen" name="screen" placeholder="Например: 6.1 дюйм"
                           value="<?= htmlspecialchars($product['specs']['screen'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500">
                </div>
                
                <!-- Процессор -->
                <div class="mb-4">
                    <label for="processor" class="block text-sm font-medium text-gray-700 mb-1">Процессор</label>
                    <input type="text" id="processor" name="processor" placeholder="Например: Snapdragon 8 Gen 3"
                           value="<?= htmlspecialchars($product['specs']['processor'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500">
                </div>
                
                <!-- Камера -->
                <div class="mb-4">
                    <label for="camera" class="block text-sm font-medium text-gray-700 mb-1">Камера</label>
                    <input type="text" id="camera" name="camera" placeholder="Например: 48 Мп"
                           value="<?= htmlspecialchars($product['specs']['camera'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500">
                </div>
            </div>
        </div>
        
        <!-- Описание -->
        <div class="mt-2 mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
            <textarea id="description" name="description" rows="6"
                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-stone-500"><?= htmlspecialchars($product['description']) ?></textarea>
        </div>
        
        <!-- Кнопки действий -->
        <div class="flex justify-end space-x-3">
            <a href="/admin/products" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md transition">
                Отмена
            </a>
            <button type="submit" class="bg-stone-600 hover:bg-stone-700 text-white font-medium py-2 px-4 rounded-md transition">
                Сохранить изменения
            </button>
        </div>
    </form>
</div>

<!-- JavaScript для формы -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const brandSelect = document.getElementById('brand');
        const newBrandContainer = document.getElementById('new-brand-container');
        const newBrandInput = document.getElementById('new_brand');
        
        // Показываем/скрываем поле для ввода нового бренда
        brandSelect.addEventListener('change', function() {
            if (brandSelect.value === 'other') {
                newBrandContainer.classList.remove('hidden');
                newBrandInput.required = true;
            } else {
                newBrandContainer.classList.add('hidden');
                newBrandInput.required = false;
            }
        });
        
        // При отправке формы, если выбран "Другой", заменяем значение бренда на введенное пользователем
        document.querySelector('form').addEventListener('submit', function(e) {
            if (brandSelect.value === 'other') {
                if (newBrandInput.value.trim()) {
                    brandSelect.value = newBrandInput.value.trim();
                } else {
                    e.preventDefault();
                    alert('Пожалуйста, введите название бренда');
                }
            }
        });
    });
</script>
