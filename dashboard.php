<?php
require 'includes/db.php';
require 'includes/auth.php';

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
<title>Dashboard - LokalKu</title>
<h1 class="text-4xl font-bold text-gray-800 mb-8 text-center">Dashboard</h1>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php if (count($memes) > 0): ?>
        <?php foreach ($memes as $meme): ?>
            <div class="bg-white border border-gray-200 shadow-lg rounded-lg overflow-hidden transform transition-transform duration-300 hover:scale-105">
                <div class="aspect-w-16 aspect-h-9 bg-gray-100">
                    <img src="/uploads/<?= htmlspecialchars($meme['image']) ?>" 
                         alt="<?= htmlspecialchars($meme['title']) ?>" 
                         class="w-full h-full object-cover">
                </div>
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-800 truncate">
                        <?= htmlspecialchars($meme['title']) ?>
                    </h2>
                    <div class="flex items-center mt-3">
                        <?php if ($meme['avatar']): ?>
                            <img src="<?= htmlspecialchars($meme['avatar']) ?>" 
                                 alt="<?= htmlspecialchars($meme['username']) ?>" 
                                 class="w-10 h-10 rounded-full border border-gray-300 shadow-sm mr-3">
                        <?php else: ?>
                            <div class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-300 mr-3 text-gray-600">
                                <i class="fas fa-user text-lg"></i>
                            </div>
                        <?php endif; ?>
                        <p class="text-sm text-gray-600">
                            Oleh: 
                            <a href="user/<?= $meme['user_id'] ?>" class="text-blue-500 hover:underline">
                                <?= htmlspecialchars($meme['username']) ?>
                            </a>
                        </p>
                    </div>
                    <p class="mt-4 text-sm font-medium text-gray-600">Votes: <?= $meme['votes'] ?></p>
                    <div class="mt-6 flex justify-between items-center">
                        <a href="dashboard?id=<?= $meme['id'] ?>&type=upvote" 
                           class="text-green-600 hover:text-green-800 flex items-center text-lg">
                            <i class="fas fa-thumbs-up mr-2"></i> Upvote
                        </a>
                        <a href="dashboard?id=<?= $meme['id'] ?>&type=downvote" 
                           class="text-red-600 hover:text-red-800 flex items-center text-lg">
                            <i class="fas fa-thumbs-down mr-2"></i> Downvote
                        </a>
                        <button 
                            onclick="openModal(<?= $meme['id'] ?>)" 
                            class="text-blue-600 hover:text-blue-800 flex items-center text-lg">
                            <i class="fas fa-comment mr-2"></i> Komentar
                        </button>
                    </div>
                    <div class="mt-4">
                        <span class="bg-blue-100 text-blue-600 text-sm font-medium px-2 py-1 rounded-full">
                            Komentar: <?= $meme['comment_count'] ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center text-gray-600 col-span-full">
            <p>Belum ada yang upload meme di LokalKu.</p>
        </div>
    <?php endif; ?>
</div>

<div id="commentModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-xl">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Tambah Komentar</h2>
        <form method="POST" action="">
            <input type="hidden" id="meme_id" name="meme_id">
            <textarea name="content" 
                      class="w-full border border-gray-300 rounded p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4" 
                      placeholder="Tulis komentar..." required></textarea>
            <div class="flex justify-end space-x-2">
                <button type="button" 
                        onclick="closeModal()" 
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Kirim
                </button>
            </div>
        </form>
        <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Komentar:</h3>
            <ul id="comments-list">
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

        comments.forEach(comment => {
            const li = document.createElement('li');
            li.classList.add('text-sm', 'text-gray-600', 'mb-2');
            li.textContent = `${comment.username}: ${comment.content}`;
            commentList.appendChild(li);
        });
    }

    function closeModal() {
        document.getElementById('commentModal').classList.add('hidden');
    }
</script>

<?php require 'templates/footer.php'; ?>
