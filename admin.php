<?php
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/i18n.php';
require 'includes/config.php';
require 'includes/security.php';

redirectIfNotLoggedIn();
redirectIfNotAdmin();

$action = $_GET['action'] ?? 'dashboard';
$settingsMessage = null;
$settingsError = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $settingsError = 'Invalid security token. Please try again.';
    } elseif (isset($_POST['action'])) {
        $postAction = $_POST['action'];
        
        if ($postAction === 'save_settings') {
            try {
                $siteName = trim($_POST['site_name'] ?? '');
                $defaultLanguage = $_POST['default_language'] ?? 'id';
                
                if (!$siteName) {
                    throw new Exception(t('admin_site_name_required'));
                }
                
                setConfig('site_name', $siteName);
                setConfig('default_language', $defaultLanguage);
                $settingsMessage = t('admin_settings_saved');
            } catch (Exception $e) {
                $settingsError = $e->getMessage();
            }
        } elseif ($postAction === 'ban_user') {
            $userId = (int)$_POST['user_id'];
            if ($userId === $_SESSION['user_id']) {
                $settingsError = 'You cannot ban yourself';
            } else {
                $stmt = $pdo->prepare("UPDATE users SET is_banned = TRUE WHERE id = ? AND id != ?");
                $stmt->execute([$userId, $_SESSION['user_id']]);
            }
        } elseif ($postAction === 'unban_user') {
            $userId = (int)$_POST['user_id'];
            $stmt = $pdo->prepare("UPDATE users SET is_banned = FALSE WHERE id = ?");
            $stmt->execute([$userId]);
        } elseif ($postAction === 'make_admin') {
            $userId = (int)$_POST['user_id'];
            if ($userId === $_SESSION['user_id']) {
                $settingsError = 'You cannot modify your own role';
            } else {
                $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ? AND id != ?");
                $stmt->execute([$userId, $_SESSION['user_id']]);
            }
        } elseif ($postAction === 'remove_admin') {
            $userId = (int)$_POST['user_id'];
            if ($userId === $_SESSION['user_id']) {
                $settingsError = 'You cannot modify your own role';
            } else {
                $stmt = $pdo->prepare("UPDATE users SET role = 'user' WHERE id = ? AND id != ?");
                $stmt->execute([$userId, $_SESSION['user_id']]);
            }
        } elseif ($postAction === 'delete_meme') {
            $memeId = (int)$_POST['meme_id'];
            $stmt = $pdo->prepare("DELETE FROM memes WHERE id = ?");
            $stmt->execute([$memeId]);
        }
    }
}

require 'templates/header.php';
?>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2"><?= t('admin_title') ?></h1>
            <p class="text-gray-600"><?= t('admin_dashboard') ?></p>
        </div>

        <div class="flex flex-wrap gap-4 mb-8 border-b border-gray-300 pb-4">
            <a href="/admin?action=dashboard" class="px-6 py-2 <?= $action === 'dashboard' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300' ?> rounded-lg hover:bg-blue-600 hover:text-white transition font-medium">
                <i class="fas fa-chart-line mr-2"></i><?= t('admin_dashboard') ?>
            </a>
            <a href="/admin?action=users" class="px-6 py-2 <?= $action === 'users' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300' ?> rounded-lg hover:bg-blue-600 hover:text-white transition font-medium">
                <i class="fas fa-users mr-2"></i><?= t('admin_users') ?>
            </a>
            <a href="/admin?action=memes" class="px-6 py-2 <?= $action === 'memes' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300' ?> rounded-lg hover:bg-blue-600 hover:text-white transition font-medium">
                <i class="fas fa-images mr-2"></i><?= t('admin_memes') ?>
            </a>
            <a href="/admin?action=settings" class="px-6 py-2 <?= $action === 'settings' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300' ?> rounded-lg hover:bg-blue-600 hover:text-white transition font-medium">
                <i class="fas fa-cog mr-2"></i><?= t('admin_settings') ?>
            </a>
        </div>

        <?php if ($action === 'dashboard'): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <?php
                $totalUsers = $pdo->query("SELECT COUNT(*) as count FROM users")->fetch()['count'];
                $totalMemes = $pdo->query("SELECT COUNT(*) as count FROM memes")->fetch()['count'];
                $totalVotes = $pdo->query("SELECT COUNT(*) as count FROM votes")->fetch()['count'];
                ?>
                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium"><?= t('admin_total_users') ?></p>
                            <p class="text-3xl font-bold text-gray-800 mt-2"><?= $totalUsers ?></p>
                        </div>
                        <i class="fas fa-users text-4xl text-blue-200"></i>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium"><?= t('admin_total_memes') ?></p>
                            <p class="text-3xl font-bold text-gray-800 mt-2"><?= $totalMemes ?></p>
                        </div>
                        <i class="fas fa-images text-4xl text-green-200"></i>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-purple-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium"><?= t('admin_total_votes') ?></p>
                            <p class="text-3xl font-bold text-gray-800 mt-2"><?= $totalVotes ?></p>
                        </div>
                        <i class="fas fa-thumbs-up text-4xl text-purple-200"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4"><?= t('admin_recent_memes') ?></h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700"><?= t('admin_meme_creator') ?></th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700"><?= t('admin_meme_date') ?></th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700"><?= t('admin_meme_votes') ?></th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700"><?= t('admin_meme_action') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("
                                SELECT m.*, u.username 
                                FROM memes m 
                                JOIN users u ON m.user_id = u.id 
                                ORDER BY m.created_at DESC 
                                LIMIT 10
                            ");
                            $memes = $stmt->fetchAll();
                            foreach ($memes as $meme):
                            ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-3 text-sm text-gray-700"><?= htmlspecialchars($meme['username']) ?></td>
                                    <td class="px-6 py-3 text-sm text-gray-700"><?= date('M d, Y', strtotime($meme['created_at'])) ?></td>
                                    <td class="px-6 py-3 text-sm text-gray-700"><?= $meme['votes'] ?></td>
                                    <td class="px-6 py-3 text-sm">
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="delete_meme">
                                            <input type="hidden" name="meme_id" value="<?= $meme['id'] ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium" onclick="return confirm('<?= t('delete') ?> meme?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($action === 'users'): ?>
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4"><?= t('admin_user_list') ?></h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700"><?= t('admin_user_id') ?></th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700"><?= t('admin_user_username') ?></th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700"><?= t('admin_user_email') ?></th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700"><?= t('admin_user_role') ?></th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700"><?= t('admin_user_joined') ?></th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700"><?= t('admin_user_action') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
                            $users = $stmt->fetchAll();
                            foreach ($users as $user):
                            ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-3 text-sm text-gray-700"><?= $user['id'] ?></td>
                                    <td class="px-6 py-3 text-sm text-gray-700">
                                        <a href="/user/<?= $user['id'] ?>" class="text-blue-600 hover:underline"><?= htmlspecialchars($user['username']) ?></a>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-700"><?= htmlspecialchars($user['email']) ?></td>
                                    <td class="px-6 py-3 text-sm">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium <?= $user['role'] === 'admin' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800' ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-700"><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                    <td class="px-6 py-3 text-sm">
                                        <div class="flex gap-2">
                                            <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                                <?php if ($user['is_banned']): ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="action" value="unban_user">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <button type="submit" class="text-green-600 hover:text-green-800 font-medium">
                                                            <i class="fas fa-check"></i> <?= t('admin_user_unban') ?>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="action" value="ban_user">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium" onclick="return confirm('<?= t('admin_user_ban') ?> user?')">
                                                            <i class="fas fa-ban"></i> <?= t('admin_user_ban') ?>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <?php if ($user['role'] === 'user'): ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="action" value="make_admin">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <button type="submit" class="text-orange-600 hover:text-orange-800 font-medium">
                                                            <i class="fas fa-crown"></i> <?= t('admin_user_make_admin') ?>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="action" value="remove_admin">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <button type="submit" class="text-gray-600 hover:text-gray-800 font-medium">
                                                            <i class="fas fa-user"></i> <?= t('admin_user_remove_admin') ?>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($action === 'memes'): ?>
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4"><?= t('admin_meme_list') ?></h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Judul</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700"><?= t('admin_meme_creator') ?></th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700"><?= t('admin_meme_date') ?></th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700"><?= t('admin_meme_votes') ?></th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700"><?= t('admin_meme_action') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("
                                SELECT m.*, u.username 
                                FROM memes m 
                                JOIN users u ON m.user_id = u.id 
                                ORDER BY m.created_at DESC
                            ");
                            $memes = $stmt->fetchAll();
                            foreach ($memes as $meme):
                            ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-3 text-sm text-gray-700"><?= $meme['id'] ?></td>
                                    <td class="px-6 py-3 text-sm text-gray-700 max-w-xs truncate"><?= htmlspecialchars($meme['title']) ?></td>
                                    <td class="px-6 py-3 text-sm text-gray-700">
                                        <a href="/user/<?= $meme['user_id'] ?>" class="text-blue-600 hover:underline"><?= htmlspecialchars($meme['username']) ?></a>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-700"><?= date('M d, Y', strtotime($meme['created_at'])) ?></td>
                                    <td class="px-6 py-3 text-sm text-gray-700"><?= $meme['votes'] ?></td>
                                    <td class="px-6 py-3 text-sm">
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="delete_meme">
                                            <input type="hidden" name="meme_id" value="<?= $meme['id'] ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium" onclick="return confirm('<?= t('delete') ?> meme?')">
                                                <i class="fas fa-trash"></i> <?= t('admin_meme_delete') ?>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($action === 'settings'): ?>
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-2xl">
                <h2 class="text-2xl font-bold text-gray-800 mb-6"><?= t('admin_settings') ?></h2>
                
                <?php if (isset($settingsMessage)): ?>
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-600 rounded-lg">
                        <p class="text-green-700 flex items-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            <?= $settingsMessage ?>
                        </p>
                    </div>
                <?php endif; ?>

                <?php if (isset($settingsError)): ?>
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-600 rounded-lg">
                        <p class="text-red-700 flex items-center gap-2">
                            <i class="fas fa-exclamation-circle"></i>
                            <?= $settingsError ?>
                        </p>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <input type="hidden" name="action" value="save_settings">
                    <?= csrfTokenInput() ?>

                    <div>
                        <label for="site_name" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-globe mr-2 text-blue-600"></i><?= t('admin_site_name') ?>
                        </label>
                        <input 
                            type="text" 
                            name="site_name" 
                            id="site_name"
                            value="<?= htmlspecialchars(getSiteName()) ?>"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition"
                            placeholder="Pixco"
                            minlength="1"
                            maxlength="50"
                            required
                        >
                        <p class="text-gray-500 text-xs mt-1"><?= t('admin_site_name_hint') ?></p>
                    </div>

                    <div>
                        <label for="default_language" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-language mr-2 text-blue-600"></i><?= t('admin_default_language') ?>
                        </label>
                        <select 
                            name="default_language" 
                            id="default_language"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition"
                        >
                            <option value="id" <?= getDefaultLanguage() === 'id' ? 'selected' : '' ?>>Indonesia</option>
                            <option value="en" <?= getDefaultLanguage() === 'en' ? 'selected' : '' ?>>English</option>
                        </select>
                        <p class="text-gray-500 text-xs mt-1"><?= t('admin_default_language_hint') ?></p>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <button 
                            type="submit" 
                            class="bg-gradient-to-r from-blue-600 to-blue-800 text-white font-bold py-3 px-6 rounded-lg hover:from-blue-700 hover:to-blue-900 transition duration-300">
                            <i class="fas fa-save mr-2"></i><?= t('admin_save_settings') ?>
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require 'templates/footer.php'; ?>
