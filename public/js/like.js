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
            button.innerHTML = `<i class="fa-solid fa-heart"></i> ${result.likesCount}`;
            button.classList.add('liked');
            localStorage.setItem(`liked_post_${postId}`, 'true');
        } else {
            button.innerHTML = `<i class="fa-regular fa-heart"></i> ${result.likesCount}`;
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
        const likesCount = button.textContent.replace(/\D+/g, '');
        const key = `liked_post_${postId}`;
        const stored = localStorage.getItem(key);
        const liked = stored === null ? button.classList.contains('liked') : stored === 'true';
        if (liked) {
            button.innerHTML = `<i class="fa-solid fa-heart"></i> ${likesCount}`;
            button.classList.add('liked');
        } else {
            button.innerHTML = `<i class="fa-regular fa-heart"></i> ${likesCount}`;
            button.classList.remove('liked');
        }
        if (stored === null) localStorage.setItem(key, liked ? 'true' : 'false');
    });
});