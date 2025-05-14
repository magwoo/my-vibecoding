<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Административная панель' ?> | ТелефонМаркет</title>
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
<body class="min-h-screen flex flex-col bg-gray-100">
    <!-- Шапка админ-панели -->
    <header class="bg-stone-700 text-white shadow-md">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <!-- Логотип -->
            <div class="flex items-center">
                <a href="/admin" class="text-xl font-bold">ТелефонМаркет</a>
                <span class="ml-2 bg-stone-600 text-xs px-2 py-1 rounded">Админ-панель</span>
            </div>
            
            <!-- Меню пользователя -->
            <?php if (isset($_SESSION['admin_id'])): ?>
            <div class="relative group">
                <button class="flex items-center hover:text-stone-300 transition">
                    <span>Администратор</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg p-2 hidden group-hover:block z-10">
                    <a href="/" class="block px-4 py-2 text-gray-800 hover:bg-stone-100 rounded" target="_blank">
                        Посетить сайт
                    </a>
                    <a href="/admin/logout" class="block px-4 py-2 text-gray-800 hover:bg-stone-100 rounded">
                        Выйти
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </header>
    
    <?php if (isset($_SESSION['admin_id'])): ?>
    <!-- Навигация админ-панели -->
    <nav class="bg-stone-800 text-white">
        <div class="container mx-auto px-4">
            <div class="flex items-center">
                <a href="/admin" class="px-4 py-3 hover:bg-stone-700 transition <?= strpos($_SERVER['REQUEST_URI'], '/admin') === 0 && $_SERVER['REQUEST_URI'] === '/admin' ? 'bg-stone-700' : '' ?>">
                    Главная
                </a>
                <a href="/admin/products" class="px-4 py-3 hover:bg-stone-700 transition <?= strpos($_SERVER['REQUEST_URI'], '/admin/products') === 0 ? 'bg-stone-700' : '' ?>">
                    Товары
                </a>
                <a href="/admin/orders" class="px-4 py-3 hover:bg-stone-700 transition <?= strpos($_SERVER['REQUEST_URI'], '/admin/orders') === 0 ? 'bg-stone-700' : '' ?>">
                    Заказы
                </a>
            </div>
        </div>
    </nav>
    <?php endif; ?>
    
    <!-- Сообщения-уведомления -->
    <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="container mx-auto px-4 py-2 mt-4">
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
