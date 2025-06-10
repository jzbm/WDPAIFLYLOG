function toggleComments(button) {
    const card = button.closest('.card');
    const commentsContainer = card.querySelector('.comments-container');
    if (!commentsContainer) return;
    
    const hiddenComments = commentsContainer.querySelectorAll('.hidden-comment');
    hiddenComments.forEach(comment => comment.classList.remove('hidden-comment'));
    button.remove();
}

