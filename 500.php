<?php require_once __DIR__ . '/includes/i18n.php'; ?>
<?php require_once __DIR__ . '/includes/config.php'; ?>
<?php require_once __DIR__ . '/templates/header.php'; ?>

<title><?= t('500_title') ?> - <?= getSiteName() ?></title>
<div class="flex items-center justify-center min-h-screen">
    <div class="text-center">
        <h1 class="text-8xl font-bold text-red-500">500</h1>
        <p class="text-2xl font-semibold text-gray-800 mt-4"><?= t('500_message') ?></p>
        <p class="text-gray-600 mt-2"><?= t('500_detail') ?></p>
        <a href="/" class="mt-6 inline-block px-6 py-3 text-white bg-blue-600 hover:bg-blue-700 rounded-lg text-lg font-medium shadow-md transition">
            <?= t('500_home') ?>
        </a>
    </div>
</div>
<?php require_once __DIR__ . '/templates/footer.php'; ?>