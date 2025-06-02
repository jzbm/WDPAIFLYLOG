<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="/public/styles/main.css" rel="stylesheet" />
    <link href="/public/styles/navbar.css" rel="stylesheet">
    <link href="/public/styles/notifications.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/8fd9367667.js" crossorigin="anonymous"></script>
    <script src="/public/js/notifications.js" defer></script>
    <title>Notifications</title>
</head>
<body>
    <nav>
        <?php include 'navbar.php'; ?>
    </nav>

    <main>
        <h2 style="text-align: center; color: #1d5694; margin-top: 30px;">Your notifications</h2>

        <?php if (!empty($notifications)): ?>
            <div class="notification-container">
                <form method="POST" action="/mark-all-as-read" style="text-align: right;">
                    <button type="submit" class="mark-all-btn">
                        <i class="fa-solid fa-check-double"></i> Mark all as read
                    </button>
                </form>

                <?php foreach ($notifications as $index => $notification): ?>
                <div class="notification-card <?= $notification->isRead() ? '' : 'unread' ?> <?= $index >= 7 ? 'hidden-notification' : '' ?>">
                    <div class="notification-content">
                        <i class="fa-solid fa-bell notification-icon"></i>
                        <div class="notification-text">
                            <p class="message"><?= $notification->getContent(); ?></p>
                            <p class="timestamp"><?= date('d.m.Y, H:i', strtotime($notification->getCreatedAt())); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>            
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #888;">No notifications.</p>
        <?php endif; ?>

        <?php if (count($notifications) > 7): ?>
        <button id="show-more-btn" onclick="showMoreNotifications()">
            Show more notifications(<?php echo count($notifications) - 7; ?>)
        </button>
        <?php endif; ?>
    </main>
</body>
</html>
