let shownNotifications = 7;
const notifications = document.querySelectorAll('.notification-card');
const showMoreBtn = document.getElementById('show-more-btn');

function showMoreNotifications() {
    const remainingNotifications = notifications.length - shownNotifications;
    const toShow = Math.min(5, remainingNotifications);

    for (let i = shownNotifications; i < shownNotifications + toShow; i++) {
        if (notifications[i]) {
            notifications[i].classList.remove('hidden-notification');
        }
    }

    shownNotifications += toShow;

    const remaining = notifications.length - shownNotifications;
    if (remaining > 0) {
        showMoreBtn.innerText = `Show more notifications (${remaining})`;
    } else {
        showMoreBtn.style.display = 'none';
    }
}

if (notifications.length <= 7) {
    showMoreBtn.style.display = 'none';
}
