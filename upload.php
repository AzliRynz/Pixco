<?php
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/i18n.php';
require 'includes/config.php';

redirectIfNotLoggedIn();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $image = $_FILES['image'];

    // Validation
    if (empty($title)) {
        $error = t('upload_error_empty_title');
    } elseif ($image['error'] !== UPLOAD_ERR_OK) {
        $error = t('upload_error_upload_failed');
    } elseif (!in_array($image['type'], ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
        $error = t('upload_error_unsupported_format');
    } elseif ($image['size'] > 5 * 1024 * 1024) {
        $error = t('upload_error_size_limit');
    } else {
        // Get safe extension
        $ext = getSafeExtension($image['type'], $image['name']);
        if (!$ext) {
            $error = t('upload_error_unsupported_format');
        } else {
            $filename = uniqid() . '.' . $ext;
            
            if (!is_dir('uploads')) {
                mkdir('uploads', 0755, true);
            }
            
            if (move_uploaded_file($image['tmp_name'], "uploads/$filename")) {
                $stmt = $pdo->prepare("INSERT INTO memes (user_id, title, image) VALUES (?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $title, $filename]);
                $success = t('upload_success');
                header('refresh:2; url=/dashboard');
            } else {
                $error = t('upload_error_save_failed');
            }
        }
    }
}

require 'templates/header.php';
?>
<div class="min-h-screen py-12 px-4">
    <div class="container mx-auto max-w-2xl">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent mb-2">
                <i class="fas fa-images text-blue-600 mr-3"></i><?= t('upload_title') ?>
            </h1>
            <p class="text-gray-600"><?= t('upload_share_desc') ?></p>
        </div>

        <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-600 rounded-lg">
                <p class="text-red-700 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </p>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-600 rounded-lg">
                <p class="text-green-700 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </p>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="bg-white shadow-2xl rounded-xl p-8 space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2" for="title">
                    <i class="fas fa-heading mr-2 text-blue-600"></i><?= t('upload_title_placeholder') ?>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition" 
                    placeholder="<?= t('upload_title_placeholder') ?>"
                    required
                >
                <p class="text-gray-500 text-xs mt-1"><?= t('upload_title_hint') ?></p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3" for="image">
                    <i class="fas fa-image mr-2 text-blue-600"></i><?= t('upload_select_file') ?>
                </label>
                <div class="relative">
                    <input 
                        type="file" 
                        name="image" 
                        id="image" 
                        accept="image/*" 
                        class="hidden" 
                        required
                        onchange="document.getElementById('filename').textContent = this.files[0] ? this.files[0].name : '<?= t('upload_no_file') ?>'"
                    >
                    <label for="image" class="block border-2 border-dashed border-blue-300 bg-blue-50 rounded-lg p-8 text-center cursor-pointer hover:bg-blue-100 transition">
                        <i class="fas fa-cloud-upload-alt text-5xl text-blue-400 mb-3"></i>
                        <p class="text-gray-700 font-semibold mb-1"><?= t('upload_drag_drop') ?></p>
                        <p class="text-gray-500 text-sm"><?= t('upload_max_size') ?></p>
                    </label>
                    <p id="filename" class="text-gray-600 text-sm mt-2"><?= t('upload_no_file') ?></p>
                </div>
            </div>

            <div class="pt-4">
                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-800 text-white font-bold py-3 px-4 rounded-lg hover:from-blue-700 hover:to-blue-900 transition duration-300 transform hover:scale-105 flex items-center justify-center gap-2">
                    <i class="fas fa-upload"></i> <?= t('upload_submit') ?>
                </button>
            </div>

            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t-2 border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500"><?= t('upload_or') ?></span>
                </div>
            </div>

            <div>
                <a href="/dashboard" class="block text-center py-3 border-2 border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-times mr-2"></i> <?= t('upload_cancel') ?>
                </a>
            </div>
        </form>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 border-l-4 border-blue-600 rounded-lg p-4">
                <p class="text-blue-700 font-semibold text-sm mb-1"><i class="fas fa-lightbulb mr-2"></i><?= t('upload_tip_1_title') ?></p>
                <p class="text-blue-600 text-sm"><?= t('upload_tip_1_desc') ?></p>
            </div>
            <div class="bg-green-50 border-l-4 border-green-600 rounded-lg p-4">
                <p class="text-green-700 font-semibold text-sm mb-1"><i class="fas fa-lightbulb mr-2"></i><?= t('upload_tip_2_title') ?></p>
                <p class="text-green-600 text-sm"><?= t('upload_tip_2_desc') ?></p>
            </div>
            <div class="bg-purple-50 border-l-4 border-purple-600 rounded-lg p-4">
                <p class="text-purple-700 font-semibold text-sm mb-1"><i class="fas fa-lightbulb mr-2"></i><?= t('upload_tip_3_title') ?></p>
                <p class="text-purple-600 text-sm"><?= t('upload_tip_3_desc') ?></p>
            </div>
        </div>
    </div>
</div>

<?php require 'templates/footer.php'; ?>