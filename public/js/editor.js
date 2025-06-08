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
        if (button) {
            if (document.queryCommandState(cmd)) {
                button.classList.add("active");
            } else {
                button.classList.remove("active");
            }
        }
    }
}
function submitForm() {
    const editor = document.getElementById('editor');
    const hiddenInput = document.getElementById('content');
    hiddenInput.value = editor.innerHTML;
}

const editorElement = document.getElementById("editor");
if (editorElement) {
    editorElement.addEventListener("keyup", updateActiveButtons);
    editorElement.addEventListener("mouseup", updateActiveButtons);
}

document.addEventListener("DOMContentLoaded", function () {
    const editor = document.getElementById("editor");
    if (!editor) return;
    editor.addEventListener("input", () => {
        if (editor.innerText.trim() === "") {
            editor.classList.add("empty");
        } else {
            editor.classList.remove("empty");
        }
    });
});
