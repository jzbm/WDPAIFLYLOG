function addCommentAjax(form) {
    const postId = form.querySelector('input[name="post_id"]').value;
    const content = form.querySelector('textarea[name="content"]').value.trim();
    const textarea = form.querySelector('textarea[name="content"]');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Adding...';

    fetch('/add-comment-ajax', {
        method: 'POST',
        body: new FormData(form)
    })
    .then(response => response.json())    .then(data => {
        if (data.success) {
            addCommentToDOM(form, data.comment);
            textarea.value = '';
        } else {
            showFeedback(data.message || 'Failed to add comment', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showFeedback('An error occurred', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Add comment';
    });
}

function addCommentToDOM(form, comment) {
    const card = form.closest('.card');
    let commentsContainer = card.querySelector('.comments-container');
    
    if (!commentsContainer) {
        commentsContainer = document.createElement('div');
        commentsContainer.className = 'comments-container';
        form.parentNode.insertBefore(commentsContainer, form);
    }
    
    const commentEl = document.createElement('div');
    commentEl.className = 'comment new-comment';
    commentEl.innerHTML = `
        <div class="comment-author">
            <a href="/messages?user=${comment.userId}">
                <img class="avatar" src="${comment.avatar}" alt="avatar">
            </a>
            <div class="author-meta">
                <a href="/messages?user=${comment.userId}">
                    <p class="nickname">${comment.nickname}</p>
                </a>
                <p class="comment-date">${comment.createdAt}</p>
            </div>
        </div>
        <p>${comment.content}</p>
    `;
    
    commentsContainer.appendChild(commentEl);
}

function showFeedback(message, type) {
    const existing = document.querySelector('.comment-feedback');
    if (existing) existing.remove();
    
    const feedback = document.createElement('div');
    feedback.className = `comment-feedback ${type}`;
    feedback.textContent = message;
    document.body.insertBefore(feedback, document.body.firstChild);
    
    setTimeout(() => feedback.remove(), 3000);
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('form[action="/add-comment"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            addCommentAjax(form);
        });
    });
});
