<?php require_once 'includes/i18n.php'; ?>
<!DOCTYPE html>
<html lang="<?= getLang() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        #mobile-menu {
            display: none;
        }
        .nav-link {
            transition: all 0.3s ease;
            position: relative;
        }
        .nav-link:hover {
            color: #fbbf24;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background-color: #fbbf24;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after {
            width: 100%;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100">
    <nav class="bg-white shadow-md border-b-2 border-blue-500 sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="/" class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent flex items-center gap-2">
                    <i class="fas fa-smile text-blue-600"></i> <?= t('brand_name') ?>
                </a>
                
                <div class="lg:hidden">
                    <button id="hamburger" class="text-gray-700 hover:text-blue-600 transition focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>

                <div class="hidden lg:flex items-center space-x-8" id="menu">
                    <a href="/leaderboard" class="nav-link text-gray-700 font-medium"><?= t('nav_leaderboard') ?></a>
                    <?php if (isLoggedIn()): ?>
                        <a href="/upload" class="nav-link text-gray-700 font-medium"><?= t('nav_upload') ?></a>
                        <a href="/user/<?= $_SESSION['user_id'] ?>" class="nav-link text-gray-700 font-medium"><?= t('nav_profile') ?></a>
                        <?php if (isAdmin()): ?>
                            <a href="/admin" class="nav-link text-gray-700 font-medium text-orange-600"><?= t('nav_admin') ?></a>
                        <?php endif; ?>
                        <a href="/settings" class="nav-link text-gray-700 font-medium"><?= t('nav_settings') ?></a>
                        <div class="relative group">
                            <button class="flex items-center gap-2 text-gray-700 hover:text-blue-600 font-medium">
                                <i class="fas fa-globe"></i>
                                <span><?= strtoupper(getLang()) ?></span>
                            </button>
                            <div class="hidden group-hover:block absolute right-0 mt-2 w-32 bg-white border border-gray-200 rounded-lg shadow-lg">
                                <a href="?lang=en" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 text-sm">English</a>
                                <a href="?lang=id" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 text-sm">Indonesia</a>
                            </div>
                        </div>
                        <a href="/logout" class="nav-link text-red-600 font-medium"><?= t('nav_logout') ?></a>
                    <?php else: ?>
                        <a href="/login" class="nav-link text-gray-700 font-medium"><?= t('nav_login') ?></a>
                        <a href="/register" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"><?= t('nav_register') ?></a>
                        <div class="relative group">
                            <button class="flex items-center gap-2 text-gray-700 hover:text-blue-600 font-medium">
                                <i class="fas fa-globe"></i>
                                <span><?= strtoupper(getLang()) ?></span>
                            </button>
                            <div class="hidden group-hover:block absolute right-0 mt-2 w-32 bg-white border border-gray-200 rounded-lg shadow-lg">
                                <a href="?lang=en" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 text-sm">English</a>
                                <a href="?lang=id" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 text-sm">Indonesia</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="lg:hidden bg-white border-b border-gray-200 shadow-md" id="mobile-menu">
        <div class="container mx-auto px-4 py-4 space-y-2">
            <a href="/leaderboard" class="block py-2 px-4 text-gray-700 hover:bg-blue-50 rounded-lg transition"><?= t('nav_leaderboard') ?></a>
            <?php if (isLoggedIn()): ?>
                <a href="/upload" class="block py-2 px-4 text-gray-700 hover:bg-blue-50 rounded-lg transition"><?= t('nav_upload') ?></a>
                <a href="/user/<?= $_SESSION['user_id'] ?>" class="block py-2 px-4 text-gray-700 hover:bg-blue-50 rounded-lg transition"><?= t('nav_profile') ?></a>
                <?php if (isAdmin()): ?>
                    <a href="/admin" class="block py-2 px-4 text-orange-600 hover:bg-orange-50 rounded-lg transition font-medium"><?= t('nav_admin') ?></a>
                <?php endif; ?>
                <a href="/settings" class="block py-2 px-4 text-gray-700 hover:bg-blue-50 rounded-lg transition"><?= t('nav_settings') ?></a>
                <div class="py-2 px-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2"><?= t('settings_language') ?></label>
                    <div class="flex gap-2">
                        <a href="?lang=en" class="flex-1 px-3 py-2 text-center text-sm <?= getLang() === 'en' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' ?> rounded-lg transition">EN</a>
                        <a href="?lang=id" class="flex-1 px-3 py-2 text-center text-sm <?= getLang() === 'id' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' ?> rounded-lg transition">ID</a>
                    </div>
                </div>
                <a href="/logout" class="block py-2 px-4 text-red-600 hover:bg-red-50 rounded-lg transition font-medium"><?= t('nav_logout') ?></a>
            <?php else: ?>
                <a href="/login" class="block py-2 px-4 text-gray-700 hover:bg-blue-50 rounded-lg transition"><?= t('nav_login') ?></a>
                <a href="/register" class="block py-2 px-4 bg-blue-600 text-white hover:bg-blue-700 rounded-lg transition text-center"><?= t('nav_register') ?></a>
                <div class="py-2 px-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2"><?= t('settings_language') ?></label>
                    <div class="flex gap-2">
                        <a href="?lang=en" class="flex-1 px-3 py-2 text-center text-sm <?= getLang() === 'en' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' ?> rounded-lg transition">EN</a>
                        <a href="?lang=id" class="flex-1 px-3 py-2 text-center text-sm <?= getLang() === 'id' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' ?> rounded-lg transition">ID</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const hamburger = document.getElementById('hamburger');
        const menu = document.getElementById('mobile-menu');
        hamburger.addEventListener('click', () => {
            menu.style.display = menu.style.display === 'none' || menu.style.display === '' ? 'block' : 'none';
        });
    </script>
    <div class="container mx-auto my-8 px-4">