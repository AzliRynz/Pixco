<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/i18n.php';

$query = "
    SELECT u.id, u.username, u.avatar, COUNT(m.id) AS total_memes, COALESCE(SUM(m.votes), 0) AS total_votes
    FROM users u
    LEFT JOIN memes m ON u.id = m.user_id
    WHERE u.is_banned = FALSE
    GROUP BY u.id
    ORDER BY total_votes DESC, total_memes DESC
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/templates/header.php';
?>

<div class="container mx-auto px-4 py-12">
    <div class="mb-12">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent mb-2">
            <i class="fas fa-trophy text-yellow-500 mr-3"></i><?= t('leaderboard_title') ?>
        </h1>
        <p class="text-gray-600 text-lg">Lihat kontributor meme terbaik di LokalKu</p>
    </div>

    <div class="bg-white shadow-2xl rounded-xl overflow-hidden">
        <div class="grid grid-cols-1 md:hidden gap-4 p-6">
            <?php foreach ($users as $rank => $user): ?>
                <div class="bg-gradient-to-r from-blue-50 to-white border-l-4 border-blue-600 p-5 rounded-lg shadow-md hover:shadow-lg transition">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3 flex-1">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white font-bold text-sm">
                                #<?= $rank + 1 ?>
                            </div>
                            <?php if (!empty($user['avatar'])): ?>
                                <img src="<?= htmlspecialchars($user['avatar'], ENT_QUOTES, 'UTF-8') ?>" 
                                     alt="<?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>" 
                                     class="w-12 h-12 rounded-full border-2 border-blue-300">
                            <?php else: ?>
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-lg">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                            <a href="/user/<?= $user['id'] ?>" class="font-bold text-gray-800 hover:text-blue-600 truncate"><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></a>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="bg-white p-2 rounded border border-gray-200">
                            <p class="text-gray-600 text-xs"><?= t('leaderboard_memes') ?></p>
                            <p class="font-bold text-lg text-blue-600"><?= (int) $user['total_memes'] ?></p>
                        </div>
                        <div class="bg-white p-2 rounded border border-gray-200">
                            <p class="text-gray-600 text-xs"><?= t('leaderboard_votes') ?></p>
                            <p class="font-bold text-lg text-yellow-500"><?= (int) $user['total_votes'] ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-blue-600 to-blue-800 text-white">
                        <th class="px-6 py-4 text-left font-bold text-sm"><?= t('leaderboard_rank') ?></th>
                        <th class="px-6 py-4 text-left font-bold text-sm">Avatar</th>
                        <th class="px-6 py-4 text-left font-bold text-sm"><?= t('leaderboard_username') ?></th>
                        <th class="px-6 py-4 text-center font-bold text-sm"><?= t('leaderboard_memes') ?></th>
                        <th class="px-6 py-4 text-center font-bold text-sm"><?= t('leaderboard_votes') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $rank => $user): ?>
                        <tr class="border-b border-gray-200 hover:bg-blue-50 transition <?= $rank < 3 ? 'bg-blue-50' : '' ?>">
                            <td class="px-6 py-4">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full font-bold text-white 
                                    <?php 
                                        if ($rank === 0) echo 'bg-yellow-500';
                                        elseif ($rank === 1) echo 'bg-gray-400';
                                        elseif ($rank === 2) echo 'bg-orange-500';
                                        else echo 'bg-blue-600';
                                    ?>
                                ">
                                    <?= $rank + 1 ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if (!empty($user['avatar'])): ?>
                                    <img src="<?= htmlspecialchars($user['avatar'], ENT_QUOTES, 'UTF-8') ?>" 
                                         alt="<?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>" 
                                         class="w-12 h-12 rounded-full border-2 border-blue-300 mx-auto">
                                <?php else: ?>
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-lg mx-auto">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-800">
                                <a href="/user/<?= $user['id'] ?>" class="hover:text-blue-600 transition"><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></a>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center gap-1 bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-bold">
                                    <i class="fas fa-images"></i> <?= (int) $user['total_memes'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center gap-1 bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm font-bold">
                                    <i class="fas fa-thumbs-up"></i> <?= (int) $user['total_votes'] ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (empty($users)): ?>
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-600 text-xl">Belum ada pengguna yang aktif</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>