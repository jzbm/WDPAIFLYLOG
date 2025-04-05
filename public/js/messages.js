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
