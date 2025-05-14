<!-- Заголовок страницы входа -->
<div class="bg-stone-600 py-6 mb-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-white">Вход в аккаунт</h1>
    </div>
</div>

<div class="container mx-auto px-4 mb-12">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h2 class="text-2xl font-semibold mb-6 text-center">Войти в систему</h2>
            
            <form action="/login" method="POST">
                <!-- CSRF токен -->
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                
                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                    <input type="email" id="email" name="email" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-stone-500 focus:border-transparent">
                </div>
                
                <!-- Пароль -->
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 font-medium mb-2">Пароль</label>
                    <input type="password" id="password" name="password" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-stone-500 focus:border-transparent">
                </div>
                
                <!-- Кнопка входа -->
                <div class="mb-6">
                    <button type="submit" class="w-full bg-stone-600 hover:bg-stone-700 text-white font-medium py-3 px-4 rounded-lg transition">
                        Войти
                    </button>
                </div>
                
                <!-- Ссылка на регистрацию -->
                <div class="text-center text-gray-600">
                    Еще нет аккаунта? <a href="/register" class="text-stone-600 hover:text-stone-800 font-medium">Зарегистрироваться</a>
                </div>
            </form>
        </div>
    </div>
</div>
