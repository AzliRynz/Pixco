<?php
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/i18n.php';

redirectIfNotLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['meme_id'], $_POST['content'])) {
    $meme_id = (int)$_POST['meme_id'];
    $content = trim($_POST['content']);

    if ($content) {
        $stmt = $pdo->prepare("INSERT INTO comments (meme_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$meme_id, $_SESSION['user_id'], $content]);
    }
    header('Location: /dashboard');
    exit();
}

$meme_id = (int)($_GET['id'] ?? 0);
$type = $_GET['type'] ?? '';

if (in_array($type, ['upvote', 'downvote'], true)) {
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

$stmt = $pdo->query("
    SELECT memes.*, 
           users.username, 
           users.avatar, 
           users.id AS user_id,
           (SELECT COUNT(*) FROM comments WHERE comments.meme_id = memes.id) AS comment_count
    FROM memes 
    JOIN users ON memes.user_id = users.id 
    ORDER BY memes.created_at DESC
");
$memes = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT comments.*, users.username, users.avatar 
                       FROM comments 
                       JOIN users ON comments.user_id = users.id 
                       WHERE comments.meme_id = ?");
require 'templates/header.php';
?>
<title><?= t('dashboard') ?> - Pixco</title>
<h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent mb-2"><?= t('dashboard') ?></h1>
<p class="text-gray-600 mb-8 text-lg">Temukan meme terbaik dari komunitas lokal</p>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php if (count($memes) > 0): ?>
        <?php foreach ($memes as $meme): ?>
            <div class="bg-white shadow-lg rounded-xl overflow-hidden transform transition-all duration-300 hover:shadow-2xl hover:scale-105">
                <div class="relative aspect-video bg-gray-100 overflow-hidden">
                    <img src="/uploads/<?= htmlspecialchars($meme['image']) ?>" 
                         alt="<?= htmlspecialchars($meme['title']) ?>" 
                         class="w-full h-full object-cover">
                    <div class="absolute top-3 right-3 bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-bold">
                        <?= $meme['votes'] ?> <?= t('votes') ?>
                    </div>
                </div>
                <div class="p-5">
                    <h2 class="text-lg font-bold text-gray-800 truncate line-clamp-2">
                        <?= htmlspecialchars($meme['title']) ?>
                    </h2>
                    <div class="flex items-center mt-4 mb-5 pb-4 border-b border-gray-200">
                        <?php if ($meme['avatar']): ?>
                            <img src="<?= htmlspecialchars($meme['avatar']) ?>" 
                                 alt="<?= htmlspecialchars($meme['username']) ?>" 
                                 class="w-10 h-10 rounded-full border-2 border-blue-300 shadow-sm mr-3">
                        <?php else: ?>
                            <div class="w-10 h-10 flex items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-blue-600 text-white mr-3 text-lg">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                        <p class="text-sm text-gray-600">
                            <span class="font-semibold"><?= htmlspecialchars($meme['username']) ?></span>
                        </p>
                    </div>
                    <div class="flex justify-between items-center gap-2">
                        <a href="dashboard?id=<?= $meme['id'] ?>&type=upvote" 
                           class="flex-1 text-center py-2 bg-green-50 text-green-600 hover:bg-green-100 rounded-lg transition font-medium text-sm">
                            <i class="fas fa-thumbs-up mr-1"></i> <?= t('upvote') ?>
                        </a>
                        <a href="dashboard?id=<?= $meme['id'] ?>&type=downvote" 
                           class="flex-1 text-center py-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition font-medium text-sm">
                            <i class="fas fa-thumbs-down mr-1"></i> <?= t('downvote') ?>
                        </a>
                        <button 
                            onclick="openModal(<?= $meme['id'] ?>)" 
                            class="flex-1 text-center py-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg transition font-medium text-sm">
                            <i class="fas fa-comment mr-1"></i> <?= $meme['comment_count'] ?>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-span-full text-center py-16">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-600 text-xl"><?= t('no_memes') ?? 'No memes yet. Start uploading!' ?></p>
        </div>
    <?php endif; ?>
</div>

<div id="commentModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-2xl">
        <h2 class="text-2xl font-bold text-gray-800 mb-4"><i class="fas fa-comments text-blue-600 mr-2"></i><?= t('comments') ?></h2>
        <form method="POST" action="">
            <input type="hidden" id="meme_id" name="meme_id">
            <textarea name="content" 
                      class="w-full border-2 border-gray-300 rounded-lg p-3 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 mb-4 resize-none" 
                      placeholder="<?= t('comment') ?>..." required rows="3"></textarea>
            <div class="flex justify-end space-x-2">
                <button type="button" 
                        onclick="closeModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-medium">
                    <?= t('cancel') ?>
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    <?= t('submit') ?>
                </button>
            </div>
        </form>
        <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-3 pb-3 border-b-2 border-gray-200"><?= t('comments') ?></h3>
            <ul id="comments-list" class="max-h-64 overflow-y-auto space-y-3">
            </ul>
        </div>
    </div>
</div>

<script>
    async function openModal(memeId) {
        document.getElementById('meme_id').value = memeId;
        const modal = document.getElementById('commentModal');
        modal.classList.remove('hidden');

        const response = await fetch(`/comment?meme_id=${memeId}`);
        const comments = await response.json();

        const commentList = document.getElementById('comments-list');
        commentList.innerHTML = '';

        if (comments.length === 0) {
            commentList.innerHTML = '<li class="text-sm text-gray-500 italic">No comments yet</li>';
        } else {
            comments.forEach(comment => {
                const li = document.createElement('li');
                li.classList.add('text-sm', 'bg-gray-50', 'p-3', 'rounded-lg', 'border-l-4', 'border-blue-400');
                li.innerHTML = `<strong class="text-gray-800">${escapeHtml(comment.username)}</strong>: ${escapeHtml(comment.content)}`;
                commentList.appendChild(li);
            });
        }
    }

    function closeModal() {
        document.getElementById('commentModal').classList.add('hidden');
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
</script>

<?php require 'templates/footer.php'; ?>
