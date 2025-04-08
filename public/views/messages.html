<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="/public/styles/main.css" rel="stylesheet" />
    <link href="/public/styles/navbar.css" rel="stylesheet" />
    <link href="/public/styles/messages.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/8fd9367667.js" crossorigin="anonymous"></script>
    <script src="/public/js/messages.js" defer></script>
    <title>Wiadomości</title>
</head>
<body>
<nav>
    <?php include 'navbar.php'; ?>
</nav>

<?php
$selectedUserId = $_GET['user'] ?? null;

if (!empty($selectedUserId)) {
    require_once __DIR__ . '/../../src/repository/UserRepository.php';
    $userRepo = new UserRepository();
    $selectedUser = $userRepo->getUserById($selectedUserId);
}
?>

<?php if (!empty($_SESSION['error_message'])): ?>
    <div class="error-banner">
        <?= htmlspecialchars($_SESSION['error_message']) ?>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<main class="messages-main <?= empty($selectedUserId) ? 'mobile-sidebar-active' : 'mobile-chat-active' ?>">
    <div class="chat-panel">
        <div class="chat-header">
            <button id="back-to-sidebar" class="back-button">&larr; Wróć</button>
            <?php if (!empty($selectedUser)): ?>
            <div class="chat-user-info">
                <img class="chat-avatar" src="<?= !empty($selectedUser['avatar']) ? htmlspecialchars($selectedUser['avatar']) : '../uploads/avatars/default.png' ?>" alt="avatar">
                <span class="chat-nickname"><?= htmlspecialchars($selectedUser['nickname'] ?? '') ?></span>
            </div>
        <?php endif; ?>
        
        </div>

        <?php if (!empty($selectedUserId)): ?>
            <div class="chat-messages" id="chat-messages"></div>

            <form method="POST" action="/send-message" class="send-form">
                <input type="hidden" name="receiver_id" value="<?= htmlspecialchars($selectedUserId) ?>" />
                <input type="text" name="content" placeholder="Napisz wiadomość..." required />
                <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
            </form>
        <?php else: ?>
            <div class="chat-placeholder">
                <img src="/public/images/empty-chat.svg" alt="Brak wiadomości" />
                <p class="no-history">Brak historii wiadomości</p>
                <p class="instruction">Wybierz rozmowę z listy po prawej lub wyszukaj użytkownika, aby rozpocząć czat.</p>
            </div>
        <?php endif; ?>
    </div>

    <aside class="sidebar">
        <input type="text" id="user-search" placeholder="Szukaj użytkownika..." />
        <ul class="user-search-results" id="user-search-results" style="display: none;"></ul>

        <h3>Ostatnie rozmowy</h3>
        <ul class="user-list" id="user-list">
            <?php foreach ($recentUsers as $user): ?>
                <li class="user-item">
                    <div class="user-item-link" data-user-id="<?= htmlspecialchars($user['id']) ?>">
                        <img src="<?= htmlspecialchars($user['avatar'] ?? '/uploads/avatars/default.png') ?>" alt="avatar">
                        <div class="user-info">
                            <p class="nickname"><?= htmlspecialchars($user['nickname']) ?></p>
                            <p class="last-message">
                                <?= isset($user['lastMessage']) ? htmlspecialchars($user['lastMessage']) : 'Brak wiadomości' ?>
                            </p>
                        </div>
                        <span class="timestamp"><?= htmlspecialchars($user['timestamp'] ?? '') ?></span>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>
</main>

<?php if (!empty($selectedUserId)): ?>
<script>
    const currentUserId = <?= json_encode($_SESSION['user_id']) ?>;
    const selectedUserId = <?= json_encode($selectedUserId) ?>;
</script>
<?php endif; ?>
<script>
    const allUsers = <?= json_encode($users ?? []) ?>;
</script>
</body>
</html>
