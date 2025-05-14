    </main>
    
    <!-- Футер админ-панели -->
    <footer class="bg-stone-800 text-white py-4 mt-auto">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; <?= date('Y') ?> ТелефонМаркет. Административная панель.</p>
        </div>
    </footer>
    
    <!-- JavaScript для админ-панели -->
    <script>
        // Функция для управления статусом заказа
        function updateOrderStatus(orderId, status) {
            if (confirm('Вы уверены, что хотите изменить статус заказа на "' + status + '"?')) {
                const formData = new FormData();
                formData.append('status', status);
                formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?? '' ?>');
                
                fetch('/admin/orders/update/' + orderId, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Произошла ошибка при обновлении статуса заказа');
                });
            }
        }
        
        // Функция для удаления товара
        function deleteProduct(productId) {
            if (confirm('Вы уверены, что хотите удалить этот товар?')) {
                const formData = new FormData();
                formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?? '' ?>');
                
                fetch('/admin/products/delete/' + productId, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Произошла ошибка при удалении товара');
                });
            }
        }
    </script>
</body>
</html>
