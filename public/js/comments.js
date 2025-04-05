function formatText(command) {
    document.execCommand(command, false, null);
    document.getElementById("editor").focus();
}

function createLink() {
    const url = prompt("Podaj adres URL:");
    if (url) {
        document.execCommand("createLink", false, url);
        document.getElementById("editor").focus();
    }
}
function updateActiveButtons() {
    const commands = {
        bold: "bold",
        italic: "italic",
        underline: "underline"
    };

    for (const cmd in commands) {
        const button = document.querySelector(`.editor-btn[onclick*="${cmd}"]`);
        if (document.queryCommandState(cmd)) {
            button.classList.add("active");
        } else {
            button.classList.remove("active");
        }
    }
}

document.getElementById("editor").addEventListener("keyup", updateActiveButtons);
document.getElementById("editor").addEventListener("mouseup", updateActiveButtons);

function submitForm() {
    const editor = document.getElementById('editor');
    const hiddenInput = document.getElementById('content');
    hiddenInput.value = editor.innerHTML;
}

function toggleComments(button, hiddenCommentsCount) {
    const commentsContainer = button.previousElementSibling;
    const hiddenComments = commentsContainer.querySelectorAll('.hidden-comment');

    hiddenComments.forEach(comment => {
        comment.classList.remove('hidden-comment');
    });

    button.style.display = 'none';
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

