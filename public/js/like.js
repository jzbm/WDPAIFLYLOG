async function toggleLike(postId, button) {
    const formData = new FormData();
    formData.append('post_id', postId);

    const response = await fetch('/like-post', {
        method: 'POST',
        body: formData
    });

    if (response.ok) {
        const result = await response.json();

        if (result.liked) {
            button.innerHTML = `❤️ ${result.likesCount}`;
            button.classList.add('liked');
            localStorage.setItem(`liked_post_${postId}`, 'true');
        } else {
            button.innerHTML = `🤍 ${result.likesCount}`;
            button.classList.remove('liked');
            localStorage.setItem(`liked_post_${postId}`, 'false');
        }
    } else {
        console.error('Failed to like post');
    }
}
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.like-btn').forEach(button => {
        const postId = button.closest('form').querySelector('input[name="post_id"]').value;
        const isLiked = localStorage.getItem(`liked_post_${postId}`) === 'true';

        if (isLiked) {
            button.innerHTML = `❤️ ${button.innerHTML.replace(/\D+/g, '')}`;
            button.classList.add('liked');
        } else {
            button.innerHTML = `🤍 ${button.innerHTML.replace(/\D+/g, '')}`;
            button.classList.remove('liked');
        }
    });
});