async function toggleLike(postId, button) {
    const formData = new FormData();
    formData.append('post_id', postId);

    const response = await fetch('/like-post', {
        method: 'POST',
        body: formData
    });

    if (response.ok) {
        const result = await response.json();
        button.innerHTML = `<i class="fa-solid fa-heart"></i> ${result.likesCount}`;
        if (result.liked) {
            button.classList.add('liked');
        } else {
            button.classList.remove('liked');
        }
    } else {
        console.error('Failed to like post');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.like-btn').forEach(button => {
        const postId = button.closest('form').querySelector('input[name="post_id"]').value;
        const isLiked = localStorage.getItem(`liked_post_${postId}`) === 'true';
        const likesCount = button.textContent.replace(/\D+/g, '');
        button.innerHTML = `<i class="fa-solid fa-heart"></i> ${likesCount}`;
        if (isLiked) {
            button.classList.add('liked');
        } else {
            button.classList.remove('liked');
        }
    });
});