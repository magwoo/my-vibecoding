<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . " - " : "" ?>Phone Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#0891b2",
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.2s ease-out',
                        'slide-down': 'slideDown 0.3s ease-out',
                    },
                }
            }
        }
    </script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        .user-dropdown {
            transition: all 0.2s ease-out;
            opacity: 0;
            transform: scale(95%) translateY(-10px);
        }
        .user-dropdown:not(.hidden) {
            opacity: 1;
            transform: scale(100%) translateY(0);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen flex flex-col">
    <!-- Announcement bar -->
    <div class="bg-gradient-to-r from-cyan-600 to-cyan-500 text-white py-2 px-4 text-center text-sm">
        <p>🎉 Free shipping on orders over $50! Use code: FREESHIP50</p>
    </div>

    <header class="glass-effect sticky top-0 z-50 border-b border-slate-200/50 shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <!-- Logo and navigation -->
                <div class="flex items-center justify-between w-full md:w-auto">
                    <a href="/" class="flex items-center space-x-2 group">
                        <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-cyan-200 transition-all duration-300">
                            <i class="fas fa-mobile-alt text-white text-xl"></i>
                        </div>
                        <span class="text-2xl font-bold bg-gradient-to-r from-cyan-600 to-cyan-500 bg-clip-text text-transparent">Phone Store</span>
                    </a>
                    
                    <!-- Mobile menu button -->
                    <button class="md:hidden bg-slate-100 p-2 rounded-lg hover:bg-slate-200 transition-colors">
                        <i class="fas fa-bars text-slate-600"></i>
                    </button>
                </div>

                <!-- Navigation and Search -->
                <div class="hidden md:flex items-center space-x-8 flex-1 justify-center">
                    <nav class="flex space-x-6">
                        <a href="/products" class="text-slate-600 hover:text-slate-900 font-medium transition-colors flex items-center space-x-1">
                            <i class="fas fa-mobile-screen-button"></i>
                            <span>Products</span>
                        </a>
                        <a href="/deals" class="text-slate-600 hover:text-slate-900 font-medium transition-colors flex items-center space-x-1">
                            <i class="fas fa-tag"></i>
                            <span>Deals</span>
                        </a>
                        <a href="/support" class="text-slate-600 hover:text-slate-900 font-medium transition-colors flex items-center space-x-1">
                            <i class="fas fa-headset"></i>
                            <span>Support</span>
                        </a>
                        <?php if (isset($_SESSION['user']) && $_SESSION['user']['is_admin']): ?>
                            <a href="/admin" class="text-slate-600 hover:text-slate-900 font-medium transition-colors flex items-center space-x-1">
                                <i class="fas fa-gear"></i>
                                <span>Admin</span>
                            </a>
                        <?php endif; ?>
                    </nav>
                    </nav>
                </div>
                
                <!-- Search and User actions -->
                <div class="flex items-center space-x-6">
                    <!-- Search button -->
                    <div class="relative hidden md:block">
                        <form action="/products/search" method="GET" class="flex">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                                <input 
                                    type="text" 
                                    name="q" 
                                    placeholder="Search products..."
                                    value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>"
                                    class="w-72 pl-10 pr-4 py-2 bg-slate-100/70 border-0 rounded-xl text-slate-900 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-cyan-500/20 transition-all duration-300"
                                >
                            </div>
                        </form>
                    </div>
                    
                    <!-- Cart -->
                    <a href="/cart" class="relative text-slate-600 hover:text-slate-900 transition-colors">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        <?php if(isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0): ?>
                            <span class="absolute -top-1 -right-1 bg-gradient-to-r from-cyan-500 to-cyan-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center shadow-lg animate-fade-in">
                                <?= $_SESSION['cart_count'] ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <!-- User menu -->
                    <?php
                    // Debug session data
                    error_log('Session data: ' . print_r($_SESSION, true));
                    
                    // Get user display info
                    $userEmail = $_SESSION['email'] ?? null;
                    $userName = $_SESSION['username'] ?? null;
                    $userId = $_SESSION['user_id'] ?? null;
                    
                    // Determine best display name
                    $displayName = $userEmail ? explode('@', $userEmail)[0] : ($userName ?: 'My Account');
                    $fullDisplayName = $userEmail ?: ($userName ?: $userId);
                    
                    if(isset($_SESSION['user_id'])): ?>
                        <div class="relative dropdown-container">
                            <button class="user-menu-trigger flex items-center space-x-3 bg-slate-100/70 hover:bg-white rounded-xl px-4 py-2 group transition-all duration-300 hover:shadow-lg">
                                <div class="w-8 h-8 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-lg flex items-center justify-center text-white shadow group-hover:shadow-cyan-200 transition-all duration-300">
                                    <i class="fas fa-user-circle text-lg"></i>
                                </div>
                                <span class="hidden md:inline text-slate-600 group-hover:text-slate-900 font-medium"><?= htmlspecialchars($displayName) ?></span>
                                <i class="fas fa-chevron-down text-xs text-slate-400 group-hover:text-slate-600"></i>
                            </button>
                            <div class="user-dropdown absolute right-0 mt-2 w-64 bg-white/80 backdrop-blur-lg rounded-xl shadow-xl py-2 border border-slate-200/50 z-50 hidden">
                                <div class="px-4 py-3 border-b border-slate-200/50">
                                    <p class="text-sm text-slate-500">Signed in as</p>
                                    <p class="text-sm font-medium text-slate-900 truncate"><?= htmlspecialchars($fullDisplayName) ?></p>
                                </div>
                                <a href="/account" class="flex items-center space-x-2 px-4 py-2 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50">
                                    <i class="fas fa-user w-5"></i>
                                    <span>My Account</span>
                                </a>
                                <a href="/orders" class="flex items-center space-x-2 px-4 py-2 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50">
                                    <i class="fas fa-box w-5"></i>
                                    <span>My Orders</span>
                                </a>
                                <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                    <a href="/admin" class="flex items-center space-x-2 px-4 py-2 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50">
                                        <i class="fas fa-gear w-5"></i>
                                        <span>Admin Panel</span>
                                    </a>
                                <?php endif; ?>
                                <div class="border-t border-slate-200/50 my-1"></div>
                                <a href="/logout" class="flex items-center space-x-2 px-4 py-2 text-sm text-red-600 hover:text-red-700 hover:bg-red-50">
                                    <i class="fas fa-sign-out-alt w-5"></i>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center space-x-3">
                            <a href="/login" class="px-4 py-2 text-slate-600 hover:text-slate-900 font-medium transition-colors">
                                Sign In
                            </a>
                            <a href="/register" class="px-4 py-2 bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-600 hover:to-cyan-700 text-white font-medium rounded-xl shadow-lg hover:shadow-cyan-200 transition-all duration-300">
                                Sign Up
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Mobile menu button -->
                    <!-- Mobile menu button -->
                    <button class="md:hidden p-2 bg-slate-100/70 hover:bg-white rounded-lg transition-colors" id="mobile-menu-button">
                        <i class="fas fa-bars text-slate-600"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile menu -->
            <div class="md:hidden hidden" id="mobile-menu">
                <div class="mt-3 space-y-1 pb-3 pt-2">
                    <form action="/products/search" method="GET" class="mb-6">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                            <input 
                                type="text" 
                                name="q" 
                                placeholder="Search products..."
                                class="w-full pl-10 pr-4 py-2 bg-slate-100 border-0 rounded-xl text-slate-900 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-cyan-500/20"
                            >
                        </div>
                    </form>

                    <!-- Navigation links -->
                    <nav class="space-y-2">
                        <a href="/products" class="flex items-center space-x-2 px-4 py-2 text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition-colors">
                            <i class="fas fa-mobile-screen-button w-5"></i>
                            <span>Products</span>
                        </a>
                        <a href="/deals" class="flex items-center space-x-2 px-4 py-2 text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition-colors">
                            <i class="fas fa-tag w-5"></i>
                            <span>Deals</span>
                        </a>
                        <a href="/support" class="flex items-center space-x-2 px-4 py-2 text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition-colors">
                            <i class="fas fa-headset w-5"></i>
                            <span>Support</span>
                        </a>
                        <?php if (isset($_SESSION['user']) && $_SESSION['user']['is_admin']): ?>
                            <a href="/admin" class="flex items-center space-x-2 px-4 py-2 text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition-colors">
                                <i class="fas fa-gear w-5"></i>
                                <span>Admin</span>
                            </a>
                        <?php endif; ?>
                    </nav>

                    <div class="border-t border-slate-200 my-4"></div>

                    <!-- User actions -->
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="space-y-2">
                            <div class="px-4 py-2">
                                <p class="text-sm text-slate-500">Signed in as</p>
                                <p class="text-sm font-medium text-slate-900 truncate"><?= isset($_SESSION['email']) ? $_SESSION['email'] : $_SESSION['user_id'] ?></p>
                            </div>
                            <a href="/account" class="flex items-center space-x-2 px-4 py-2 text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition-colors">
                                <i class="fas fa-user w-5"></i>
                                <span>My Account</span>
                            </a>
                            <a href="/orders" class="flex items-center space-x-2 px-4 py-2 text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition-colors">
                                <i class="fas fa-box w-5"></i>
                                <span>My Orders</span>
                            </a>
                            <a href="/logout" class="flex items-center space-x-2 px-4 py-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                <i class="fas fa-sign-out-alt w-5"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="space-y-3 px-4">
                            <a href="/login" class="block w-full px-4 py-2 text-center text-slate-600 hover:text-slate-900 font-medium border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                                Sign In
                            </a>
                            <a href="/register" class="block w-full px-4 py-2 text-center text-white font-medium bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-600 hover:to-cyan-700 rounded-xl shadow-lg hover:shadow-cyan-200 transition-all">
                                Sign Up
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile menu functionality
        const mobileMenuButton = document.querySelector('#mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // User dropdown functionality
        const userMenuTrigger = document.querySelector('.user-menu-trigger');
        const userDropdown = document.querySelector('.user-dropdown');

        if (userMenuTrigger && userDropdown) {
            // Show dropdown on click
            userMenuTrigger.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                // Toggle dropdown
                const isHidden = userDropdown.classList.contains('hidden');
                if (isHidden) {
                    userDropdown.classList.remove('hidden');
                    // Give the browser a moment to process the display change
                    requestAnimationFrame(() => {
                        userDropdown.style.opacity = '1';
                        userDropdown.style.transform = 'scale(100%) translateY(0)';
                    });
                } else {
                    userDropdown.style.opacity = '0';
                    userDropdown.style.transform = 'scale(95%) translateY(-10px)';
                    // Wait for animation to finish before hiding
                    setTimeout(() => {
                        userDropdown.classList.add('hidden');
                    }, 200);
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!userDropdown.classList.contains('hidden') && 
                    !userDropdown.contains(e.target) && 
                    !userMenuTrigger.contains(e.target)) {
                    userDropdown.style.opacity = '0';
                    userDropdown.style.transform = 'scale(95%) translateY(-10px)';
                    setTimeout(() => {
                        userDropdown.classList.add('hidden');
                    }, 200);
                }
            });

            // Close dropdown when pressing escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !userDropdown.classList.contains('hidden')) {
                    userDropdown.style.opacity = '0';
                    userDropdown.style.transform = 'scale(95%) translateY(-10px)';
                    setTimeout(() => {
                        userDropdown.classList.add('hidden');
                    }, 200);
                }
            });
        }
    });
</script>
    
    <!-- Flash messages -->
    <?php if (isset($_SESSION["success"])): ?>
        <div id="success-message" class="hidden"><?= $_SESSION["success"] ?></div>
        <?php unset($_SESSION["success"]); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION["error"])): ?>
        <div id="error-message" class="hidden"><?= $_SESSION["error"] ?></div>
        <?php unset($_SESSION["error"]); ?>
    <?php endif; ?>
    
    <main class="flex-grow container mx-auto px-4 py-6">
