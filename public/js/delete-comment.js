function deleteComment(commentId, button) {
    if (!confirm('Are you sure you want to delete this comment?')) {
        return;
    }

    button.disabled = true;
    button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

    fetch('/delete-comment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `comment_id=${commentId}`
    })
    .then(response => response.json())
    .then(data => {        if (data.success) {
            const commentElement = button.closest('.comment');
            if (commentElement) {
                commentElement.style.transition = 'opacity 0.3s ease';
                commentElement.style.opacity = '0';
                setTimeout(() => {
                    commentElement.remove();
                }, 300);
            }
        } else {
            if (typeof showFeedback === 'function') {
                showFeedback(data.message || 'Failed to delete comment', 'error');
            }
            button.disabled = false;
            button.innerHTML = '<i class="fa-solid fa-trash"></i>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof showFeedback === 'function') {
            showFeedback('An error occurred while deleting the comment', 'error');
        }
        button.disabled = false;
        button.innerHTML = '<i class="fa-solid fa-trash"></i>';
    });
}
