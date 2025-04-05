<nav class="navbar">
    <div class="nav-left">
        <a href="/dashboard" class="nav-item">
            <i class="fa-solid fa-house"></i> Main Page
        </a>
    </div>
    <div class="nav-right">
        <?php if (isset($_SESSION['user'])): ?>
            <?php
                require_once __DIR__ . '/../../src/repository/NotificationRepository.php';
                $notificationRepo = new NotificationRepository();
                $db = Database::getInstance()->connect();
                $stmt = $db->prepare('SELECT id FROM auth WHERE email = :email');
                $stmt->bindParam(':email', $_SESSION['user'], PDO::PARAM_STR);
                $stmt->execute();
                $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
                $userId = $userRow['id'] ?? null;    // cwel mi psul profil przez nadpisywanie zmiennej tablico

                $unreadCount = $userId ? $notificationRepo->countUnreadNotifications($userId) : 0;
            ?>

            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="/user-management" class="nav-item">
                    <i class="fa-solid fa-gears"></i> Control Panel
                </a>
            <?php endif; ?>

            <a href="/profile" class="nav-item">
                <i class="fa-solid fa-user"></i> Profile
            </a>

            <a href="/notifications" class="nav-item">
                <i class="fa-solid fa-bell"></i> Notifications
                <?php if ($unreadCount > 0): ?>
                    <span class="notification-badge"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>

            <a href="/messages" class="nav-item">
                <i class="fa-solid fa-envelope"></i> Messages
            </a>

            <a href="/logout" class="nav-item">
                <i class="fa-solid fa-sign-out-alt"></i> Logout
            </a>
        <?php else: ?>
            <a href="/login" class="nav-item">
                <i class="fa-solid fa-sign-in-alt"></i> Login
            </a>
            <a href="/register" class="nav-item">
                <i class="fa-solid fa-user-plus"></i> Register
            </a>
        <?php endif; ?>
    </div>
</nav>
