document.addEventListener('DOMContentLoaded', () => {
    const btn  = document.getElementById('toggle-post-form');
    const form = document.getElementById('post-form-container');

    btn.addEventListener('click', () => {
        form.classList.toggle('show');
        btn.style.display = 'none';
    });
});