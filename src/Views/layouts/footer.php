    </main>
    
    <footer class="bg-stone-800 text-white py-6">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between">
                <div class="mb-4 md:mb-0">
                    <h3 class="text-lg font-semibold mb-2">About Phone Store</h3>
                    <p class="text-stone-300">The latest and greatest phones at competitive prices.</p>
                </div>
                
                <div class="mb-4 md:mb-0">
                    <h3 class="text-lg font-semibold mb-2">Quick Links</h3>
                    <ul class="text-stone-300">
                        <li><a href="/" class="hover:text-white transition">Home</a></li>
                        <li><a href="/products" class="hover:text-white transition">Products</a></li>
                        <li><a href="/contact" class="hover:text-white transition">Contact Us</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-2">Contact</h3>
                    <p class="text-stone-300">Email: info@phonestore.com</p>
                    <p class="text-stone-300">Phone: +1 (123) 456-7890</p>
                    <div class="mt-2 flex space-x-4">
                        <a href="#" class="text-stone-300 hover:text-white transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-stone-300 hover:text-white transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-stone-300 hover:text-white transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 pt-6 border-t border-stone-700 text-center text-stone-400">
                <p>&copy; <?= date('Y') ?> Phone Store. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- Custom JavaScript -->
    <script src="/js/script.js"></script>
    <script src="/js/notifications.js"></script>
</body>
</html>
