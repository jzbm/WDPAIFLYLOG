<script src="/public/js/navbar.js" defer></script>
<nav class="navbar">
    <div class="nav-left">
        <a href="/dashboard" class="nav-item">
            <i class="fa-solid fa-house"></i> Main Page
        </a>
    </div>
    <button class="menu-toggle" id="menu-toggle">
        <i class="fa fa-bars"></i>
    </button>
    <div class="nav-right">
        <?php if (isset($_SESSION['user'])): ?>
            <?php $count = $unreadCount ?? 0; ?>

            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="/user-management" class="nav-item">
                    <i class="fa-solid fa-gears"></i> Control Panel
                </a>
            <?php endif; ?>

            <a href="/profile" class="nav-item">
                <i class="fa-solid fa-user"></i> Profile
            </a>

            <a href="/notifications" class="nav-item<?= $count > 0 ? ' has-unread' : '' ?>">
                <i class="fa-solid fa-bell"></i> Notifications
            </a>

            <?php $msgCount = $messageUnreadCount ?? 0; ?>
            <a href="/messages" class="nav-item<?= $msgCount > 0 ? ' has-unread' : '' ?>">
                <i class="fa-solid fa-envelope"></i> Messages
                <?php if ($msgCount > 0): ?>
                    <span class="badge"><?= $msgCount ?></span>
                <?php endif; ?>
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
