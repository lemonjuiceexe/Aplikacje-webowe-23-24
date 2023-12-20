let refreshing = false;
let username = "";
const joinTimestamp = new Date();

window.onload = () => {
    document.querySelector(".fakeScroll__content").scrollTop = document.querySelector(".fakeScroll__content").scrollHeight;
    document.querySelector(".entry-input").focus();
    document.querySelector(".history").fakeScroll({});
    username = prompt("Enter your username:");
}

// Send message on button click or enter key press
document.querySelector(".entry-submit").addEventListener("click", () => {
    const message = document.querySelector(".entry-input").value;
    sendMessage(username, message);
});
document.addEventListener("keydown", event => {
    if (event.key === "Enter" && !event.shiftKey) {
        const message = document.querySelector(".entry-input").value;
        sendMessage(username, message);
    }
});

async function refreshMessages(longPolling) {
    const startTime = new Date();
    const response = await fetch(longPolling ? `get_messages_long.php` : `get_messages_instant.php`);
    const endTime = new Date();
    const data = await response.json();
    console.log(`Refreshed messages in ${endTime - startTime}ms`);
    document.querySelector(".fakeScroll__content").innerHTML = "";
    data.forEach(message => {
        if(new Date(message.timestamp) < joinTimestamp)
            return;
        document.querySelector(".fakeScroll__content").innerHTML += `
                    <div class="message">
                        <div class="message-header">
                            <p class="message-author">${message.user}</p>
                            <p class="message-timestamp">${message.formatted_timestamp}</p>
                        </div>
                        <p class="message-content">${message.message}</p>
                    </div>
                    `;
    });
    $(".message-content").emoticonize();
    document.querySelector(".fakeScroll__content").scrollTop = document.querySelector(".fakeScroll__content").scrollHeight;
}
function sendMessage(user, message) {
    if (!message || !user) {
        return;
    }
    fetch(`send.php`, {
        method: "POST",
        body: JSON.stringify({
            user: user,
            message: message
        }),
        headers: {
            "Content-Type": "application/json",
        }
    })
        .then(response => response.text())
        .then(data => {
            console.log(data);
        });
    document.querySelector(".entry-input").value = "";
    if (!refreshing) {
        // Constantly refresh messages using long polling
        (async () => {
            while (true) {
                await refreshMessages(true);
            }
        })();
        refreshing = true;
    }
}