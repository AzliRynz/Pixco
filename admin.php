<?php
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/i18n.php';

redirectIfNotLoggedIn();
redirectIfNotAdmin();

$action = $_GET['action'] ?? 'dashboard';

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $postAction = $_POST['action'];
        
        if ($postAction === 'ban_user') {
            $userId = (int)$_POST['user_id'];
            $stmt = $pdo->prepare("UPDATE users SET is_banned = TRUE WHERE id = ? AND id != ?");
            $stmt->execute([$userId, $_SESSION['user_id']]);
        } elseif ($postAction === 'unban_user') {
            $userId = (int)$_POST['user_id'];
            $stmt = $pdo->prepare("UPDATE users SET is_banned = FALSE WHERE id = ?");
            $stmt->execute([$userId]);
        } elseif ($postAction === 'make_admin') {
            $userId = (int)$_POST['user_id'];
            $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ? AND id != ?");
            $stmt->execute([$userId, $_SESSION['user_id']]);
        } elseif ($postAction === 'remove_admin') {
            $userId = (int)$_POST['user_id'];
            $stmt = $pdo->prepare("UPDATE users SET role = 'user' WHERE id = ? AND id != ?");
            $stmt->execute([$userId, $_SESSION['user_id']]);
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
                <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Memes</h2>
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
    </div>
</div>

<?php require 'templates/footer.php'; ?>
