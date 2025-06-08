document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("user-search");
    const userList = document.getElementById("user-list");

    if (searchInput && typeof allUsers !== 'undefined') {
        const searchResultsContainer = document.createElement("ul");
        searchResultsContainer.id = "search-results";
        searchResultsContainer.classList.add("user-list");
        userList.parentNode.insertBefore(searchResultsContainer, userList.nextSibling);

        searchInput.addEventListener("input", function () {
            const filter = this.value.toLowerCase();
            searchResultsContainer.innerHTML = "";

            if (filter.length === 0) {
                searchResultsContainer.style.display = "none";
                userList.style.display = "block";
                return;
            }

            const matched = allUsers.filter(u =>
                u.nickname.toLowerCase().includes(filter)
            );

            matched.forEach(user => {
                const li = document.createElement("li");
                li.className = "user-item";
                li.innerHTML = `
                    <a href="/messages?user=${user.id}" style="text-decoration: none; color: inherit;">
                        <img src="${user.avatar || '/uploads/avatars/default.png'}" alt="avatar">
                        <div class="user-info">
                            <p class="nickname">${user.nickname}</p>
                            <p class="last-message">No messages</p>
                        </div>
                        <span class="timestamp"></span>
                    </a>
                `;
                searchResultsContainer.appendChild(li);
            });

            searchResultsContainer.style.display = "block";
            userList.style.display = "none";
        });
    }

    if (typeof selectedUserId !== 'undefined') {
        let isUserScrolling = false;
        let lastScrollTop = 0;
        function fetchMessages() {
            fetch(`/get-messages-ajax?user=${selectedUserId}`)
                .then(res => res.json())
                .then(messages => {
                    const container = document.getElementById("chat-messages");
                    if (!container) return;
                    const isAtBottom = Math.abs(container.scrollTop + container.clientHeight - container.scrollHeight) < 5;
                    let prevScrollHeight = container.scrollHeight;
                    let prevScrollTop = container.scrollTop;
                    container.innerHTML = "";
                    messages.forEach(msg => {
                        const div = document.createElement("div");
                        div.className = "message-bubble " + (msg.sender_id == currentUserId ? "sent" : "received");
                        div.innerHTML = `<p>${msg.content}</p><span>${new Date(msg.created_at).toLocaleString()}</span>`;
                        container.appendChild(div);
                    });
                    if (isAtBottom) {
                        container.scrollTop = container.scrollHeight;
                    } else {
                        container.scrollTop = container.scrollHeight - prevScrollHeight + prevScrollTop;
                    }
                });
        }

        setInterval(fetchMessages, 5000);
        fetchMessages();
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const userItems = document.querySelectorAll(".user-item a");
    const messagesMain = document.querySelector(".messages-main");
    const backButton = document.getElementById("back-to-sidebar");

    if (window.innerWidth <= 768) {
        messagesMain.classList.add("mobile-sidebar-active");
    }

    userItems.forEach(item => {
        item.addEventListener("click", () => {
            if (window.innerWidth <= 768) {
                messagesMain.classList.remove("mobile-sidebar-active");
                messagesMain.classList.add("mobile-chat-active");
            }
        });
    });

    if (backButton) {
        backButton.addEventListener("click", () => {
            messagesMain.classList.remove("mobile-chat-active");
            messagesMain.classList.add("mobile-sidebar-active");
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const messagesMain = document.querySelector(".messages-main");
    const backButton = document.getElementById("back-to-sidebar");

    const userItems = document.querySelectorAll(".user-item-link");
    userItems.forEach(item => {
        item.addEventListener("click", () => {
            const userId = item.getAttribute("data-user-id");
            if (!userId) return;

            if (window.innerWidth <= 768) {
                messagesMain.classList.remove("mobile-sidebar-active");
                messagesMain.classList.add("mobile-chat-active");
            }

            window.location.href = `/messages?user=${userId}`;
        });
    });

    if (backButton) {
        backButton.addEventListener("click", () => {
            messagesMain.classList.remove("mobile-chat-active");
            messagesMain.classList.add("mobile-sidebar-active");
        });
    }

    if (window.innerWidth <= 768) {
        if (typeof selectedUserId !== 'undefined') {
            messagesMain.classList.add("mobile-chat-active");
        } else {
            messagesMain.classList.add("mobile-sidebar-active");
        }
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const links = document.querySelectorAll(".user-item-link");
    links.forEach(link => {
        link.addEventListener("click", () => {
            const userId = link.getAttribute("data-user-id");
            if (userId) {
                window.location.href = `/messages?user=${userId}`;
            }
        });
    });
});

// wysylanie ajax

document.addEventListener("DOMContentLoaded", function () {
    const sendForm = document.querySelector(".send-form");
    const chatMessages = document.getElementById("chat-messages");
    if (sendForm && chatMessages) {
        sendForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(sendForm);
            const content = formData.get("content").trim();
            if (!content) return;
            fetch("/send-message", {
                method: "POST",
                body: formData
            })
            .then(res => {
                if (!res.ok) throw new Error("Message sending error");
                return res.text();
            })
            .then(() => {
                sendForm.reset();
                if (typeof fetchMessages === "function") fetchMessages();
            })
            .catch(() => {
                alert("Failed to send message.");
            });
        });
    }
});
