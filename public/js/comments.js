function toggleComments(button) {
    const card = button.closest('.card');
    const commentsContainer = card.querySelector('.comments-container');
    if (!commentsContainer) return;
    const hiddenComments = commentsContainer.querySelectorAll('.hidden-comment');
    hiddenComments.forEach(comment => comment.classList.remove('hidden-comment'));
    button.remove();
}

document.addEventListener("DOMContentLoaded", function () {
    const editor = document.getElementById("editor");

    editor.addEventListener("input", () => {
        if (editor.innerText.trim() === "") {
            editor.classList.add("empty");
        } else {
            editor.classList.remove("empty");
        }
    });
});

