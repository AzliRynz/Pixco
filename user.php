<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/i18n.php';

redirectIfNotLoggedIn();

$user_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?? $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT id, username, avatar FROM users WHERE id = ? AND is_banned = FALSE");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(404);
    die(t('not_found'));
}

$stmt = $pdo->prepare("SELECT * FROM memes WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$memes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$meme_id = filter_input(INPUT_GET, 'meme_id', FILTER_VALIDATE_INT);
$type = htmlspecialchars($_GET['type'] ?? '');

if ($meme_id && in_array($type, ['upvote', 'downvote'], true)) {
    $stmt = $pdo->prepare("SELECT * FROM votes WHERE user_id = ? AND meme_id = ?");
    $stmt->execute([$_SESSION['user_id'], $meme_id]);
    $existingVote = $stmt->fetch();

    if (!$existingVote) {
        $stmt = $pdo->prepare("INSERT INTO votes (user_id, meme_id, vote_type) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $meme_id, $type]);

        $voteChange = ($type === 'upvote') ? 1 : -1;
        $stmt = $pdo->prepare("UPDATE memes SET votes = votes + ? WHERE id = ?");
        $stmt->execute([$voteChange, $meme_id]);
    }
}

require_once __DIR__ . '/templates/header.php';
?>

<title><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?> - Pixco</title>

<div class="min-h-screen py-12 px-4">
    <div class="container mx-auto max-w-4xl">
        <div class="text-center mb-12">
            <?php if ($user['avatar']): ?>
                <img src="<?= htmlspecialchars($user['avatar'], ENT_QUOTES, 'UTF-8') ?>" 
                     alt="<?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>" 
                     class="w-32 h-32 rounded-full mx-auto mb-6 border-4 border-blue-300 shadow-lg">
            <?php else: ?>
                <div class="w-32 h-32 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 mx-auto mb-6 flex items-center justify-center text-white border-4 border-blue-300 shadow-lg">
                    <i class="fas fa-user text-5xl"></i>
                </div>
            <?php endif; ?>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <div class="flex justify-center gap-6 mt-4">
                <span class="text-gray-600"><i class="fas fa-images text-blue-600 mr-2"></i><?= count($memes) ?> Meme</span>
                <?php if ($user['id'] === $_SESSION['user_id']): ?>
                    <a href="/settings" class="text-blue-600 hover:text-blue-800"><i class="fas fa-cog mr-2"></i>Edit Profil</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-images text-blue-600 mr-2"></i><?= t('profile_memes') ?>
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (count($memes) > 0): ?>
                    <?php foreach ($memes as $meme): ?>
                        <div class="bg-white shadow-lg rounded-xl overflow-hidden transform transition-all duration-300 hover:shadow-2xl hover:scale-105">
                            <div class="relative aspect-video bg-gray-100 overflow-hidden">
                                <img 
                                    src="/uploads/<?= htmlspecialchars($meme['image'], ENT_QUOTES, 'UTF-8') ?>" 
                                    alt="<?= htmlspecialchars($meme['title'], ENT_QUOTES, 'UTF-8') ?>" 
                                    class="w-full h-full object-cover">
                                <div class="absolute top-3 right-3 bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-bold">
                                    <?= $meme['votes'] ?> <?= t('votes') ?>
                                </div>
                            </div>
                            <div class="p-5">
                                <h3 class="text-lg font-bold text-gray-800 line-clamp-2">
                                    <?= htmlspecialchars($meme['title']) ?>
                                </h3>
                                <p class="text-gray-500 text-xs mt-2"><?= date('M d, Y', strtotime($meme['created_at'])) ?></p>
                                <div class="flex justify-between items-center gap-2 mt-4">
                                    <a href="javascript:void(0)" 
                                       onclick="vote(<?= $meme['id'] ?>, 'upvote')" 
                                       class="flex-1 text-center py-2 bg-green-50 text-green-600 hover:bg-green-100 rounded-lg transition font-medium text-sm">
                                        <i class="fas fa-thumbs-up mr-1"></i> <?= t('upvote') ?>
                                    </a>
                                    <a href="javascript:void(0)" 
                                       onclick="vote(<?= $meme['id'] ?>, 'downvote')" 
                                       class="flex-1 text-center py-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition font-medium text-sm">
                                        <i class="fas fa-thumbs-down mr-1"></i> <?= t('downvote') ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-600 text-lg"><?= t('profile_memes') ?> belum ada</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>