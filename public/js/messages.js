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
                        <img src="${user.avatar || '/public/images/default-avatar.png'}" alt="avatar">
                        <div class="user-info">
                            <p class="nickname">${user.nickname}</p>
                            <p class="last-message">Brak wiadomości</p>
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
        function fetchMessages() {
            fetch(`/get-messages-ajax?user=${selectedUserId}`)
                .then(res => res.json())
                .then(messages => {
                    const container = document.getElementById("chat-messages");
                    if (!container) return;
                    container.innerHTML = "";

                    messages.forEach(msg => {
                        const div = document.createElement("div");
                        div.className = "message-bubble " + (msg.sender_id == currentUserId ? "sent" : "received");
                        div.innerHTML = `<p>${msg.content}</p><span>${new Date(msg.created_at).toLocaleString()}</span>`;
                        container.appendChild(div);
                    });

                    container.scrollTop = container.scrollHeight;
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
