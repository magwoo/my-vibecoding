<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Интернет-магазин телефонов' ?></title>
    <!-- Подключаем Tailwind CSS через CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Настройка Tailwind с акцентным цветом stone-600
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        accent: colors.stone["600"]
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen flex flex-col bg-gray-50">
    <!-- Хедер -->
    <header class="bg-stone-600 text-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex flex-col md:flex-row items-center justify-between">
            <!-- Логотип -->
            <a href="/" class="text-2xl font-bold mb-2 md:mb-0">ТелефонМаркет</a>
            
            <!-- Поиск -->
            <div class="w-full md:w-1/3 mb-2 md:mb-0">
                <form action="/catalog" method="GET" class="flex">
                    <input type="text" name="search" placeholder="Поиск телефонов..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" 
                        class="w-full px-4 py-2 rounded-l text-gray-700 focus:outline-none">
                    <button type="submit" class="bg-stone-800 px-4 py-2 rounded-r hover:bg-stone-900 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>
            </div>
            
            <!-- Навигация -->
            <nav class="flex items-center space-x-6">
                <!-- Каталог -->
                <a href="/catalog" class="hover:text-stone-300 transition">Каталог</a>
                
                <!-- Корзина -->
                <a href="/cart" class="relative hover:text-stone-300 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <?php 
                    $cartCount = 0;
                    if (isset($_SESSION['user_id'])) {
                        $cartModel = new \Models\Cart();
                        $cartCount = $cartModel->getCount();
                    }
                    if ($cartCount > 0): 
                    ?>
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                        <?= $cartCount ?>
                    </span>
                    <?php endif; ?>
                </a>
                
                <!-- Авторизация или Личный кабинет -->
                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="relative group">
                    <button class="flex items-center hover:text-stone-300 transition py-2">
                        <span>Аккаунт</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="absolute right-0 w-48 bg-white rounded-md shadow-lg hidden group-hover:block z-10 -mt-2">
                        <div class="h-2"></div>
                        <div class="py-2">
                            <a href="/account" class="block px-4 py-2 text-gray-800 hover:bg-stone-100 rounded">Личный кабинет</a>
                            <a href="/account/orders" class="block px-4 py-2 text-gray-800 hover:bg-stone-100 rounded">Мои заказы</a>
                            <a href="/logout" class="block px-4 py-2 text-gray-800 hover:bg-stone-100 rounded">Выйти</a>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="flex space-x-3">
                    <a href="/login" class="hover:text-stone-300 transition">Вход</a>
                    <a href="/register" class="hover:text-stone-300 transition">Регистрация</a>
                </div>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    
    <!-- Сообщения-уведомления -->
    <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="container mx-auto px-4 py-2">
        <div class="<?= $_SESSION['flash_message']['type'] === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700' ?> px-4 py-3 rounded relative border" role="alert">
            <span class="block sm:inline"><?= $_SESSION['flash_message']['message'] ?></span>
        </div>
    </div>
    <?php 
    // Удаляем сообщение после отображения
    unset($_SESSION['flash_message']);
    endif; 
    ?>
    
    <!-- Основной контент -->
    <main class="flex-grow container mx-auto px-4 py-6">
