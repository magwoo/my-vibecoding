<!-- Заголовок личного кабинета -->
<div class="bg-stone-600 py-6 mb-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-white">Личный кабинет</h1>
    </div>
</div>

<div class="container mx-auto px-4 mb-12">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Навигация личного кабинета -->
        <div class="lg:w-1/4">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Меню</h2>
                <nav class="space-y-2">
                    <a href="/account" class="block px-4 py-2 bg-stone-100 text-stone-800 rounded-md font-medium">
                        Мой профиль
                    </a>
                    <a href="/account/orders" class="block px-4 py-2 text-gray-700 hover:bg-stone-50 rounded-md">
                        История заказов
                    </a>
                    <a href="/logout" class="block px-4 py-2 text-gray-700 hover:bg-stone-50 rounded-md">
                        Выйти
                    </a>
                </nav>
            </div>
        </div>
        
        <!-- Контент личного кабинета -->
        <div class="lg:w-3/4">
            <!-- Информация профиля -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4">Данные аккаунта</h2>
                
                <div class="space-y-4">
                    <div>
                        <span class="block text-gray-600 mb-1">Email:</span>
                        <span class="font-medium"><?= htmlspecialchars($user['email']) ?></span>
                    </div>
                    <div>
                        <span class="block text-gray-600 mb-1">Дата регистрации:</span>
                        <span class="font-medium"><?= date('d.m.Y', strtotime($user['created_at'])) ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Последние заказы -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Недавние заказы</h2>
                    <a href="/account/orders" class="text-stone-600 hover:text-stone-800 font-medium">
                        Показать все
                    </a>
                </div>
                
                <?php if (empty($recentOrders)): ?>
                <div class="text-center py-6">
                    <p class="text-gray-600">У вас пока нет заказов</p>
                    <a href="/catalog" class="mt-3 inline-block text-stone-600 hover:text-stone-800 font-medium">
                        Перейти в каталог
                    </a>
                </div>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">№ заказа</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Дата</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Сумма</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Статус</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                            <tr class="border-b border-gray-200">
                                <td class="py-3 px-4 text-sm"><?= $order['id'] ?></td>
                                <td class="py-3 px-4 text-sm"><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                                <td class="py-3 px-4 text-sm font-medium"><?= number_format($order['total_amount'], 0, '.', ' ') ?> ₽</td>
                                <td class="py-3 px-4">
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full 
                                        <?php
                                        $statusColor = '';
                                        switch ($order['status']) {
                                            case 'в обработке':
                                                $statusColor = 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'оплачен':
                                                $statusColor = 'bg-blue-100 text-blue-800';
                                                break;
                                            case 'отправлен':
                                                $statusColor = 'bg-indigo-100 text-indigo-800';
                                                break;
                                            case 'доставлен':
                                                $statusColor = 'bg-green-100 text-green-800';
                                                break;
                                            case 'отменен':
                                                $statusColor = 'bg-red-100 text-red-800';
                                                break;
                                            default:
                                                $statusColor = 'bg-gray-100 text-gray-800';
                                        }
                                        echo $statusColor;
                                        ?>">
                                        <?= htmlspecialchars($order['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="/account/order/<?= $order['id'] ?>" class="text-stone-600 hover:text-stone-800 font-medium">
                                        Детали
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
