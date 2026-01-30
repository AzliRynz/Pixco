<?php
require_once __DIR__ . '/includes/i18n.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/templates/header.php';
?>
<title><?= t('404_title') ?> - <?= getSiteName() ?></title>
<body class="bg-gray-100 flex items-center justify-center h-screen">
  <div class="text-center">
    <h1 class="text-6xl font-bold text-gray-800">404</h1>
    <p class="text-xl text-gray-600 mt-4"><?= t('404_message') ?></p>
    <a href="/" class="mt-6 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
      <?= t('404_home') ?>
    </a>
  </div>
<?php require_once __DIR__ . '/templates/footer.php'; ?>