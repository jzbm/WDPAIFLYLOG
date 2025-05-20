document.addEventListener('DOMContentLoaded', () => {
    const btn  = document.getElementById('toggle-post-form');
    const form = document.getElementById('post-form-container');

    btn.addEventListener('click', () => {
        form.classList.add('show');
        btn.style.transition = 'opacity 0.3s ease';
        btn.style.opacity = '0';
        btn.style.pointerEvents = 'none';
        btn.addEventListener('transitionend', () => {
            btn.style.display = 'none';
        }, { once: true });
    });
});