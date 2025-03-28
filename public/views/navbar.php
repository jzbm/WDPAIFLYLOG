<nav class="navbar">
    <div class="nav-left">
        <a href="/dashboard" class="nav-item">
            <i class="fa-solid fa-house"></i> Main Page
        </a>
    </div>
    <div class="nav-right">
        <?php if (isset($_SESSION['user'])): ?>
            <a href="/profile" class="nav-item">
                <i class="fa-solid fa-user"></i> Profile
            </a>
            <a href="/notifications" class="nav-item">
                <i class="fa-solid fa-bell"></i> Notifications
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
