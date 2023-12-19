let refreshing = false;

document.querySelector(".history").fakeScroll({});

// Send message on button click or enter key press
document.querySelector(".entry-submit").addEventListener("click", () => {
    const message = document.querySelector(".entry-input").value;
    sendMessage("8O", message);
});
document.addEventListener("keydown", event => {
    if (event.key === "Enter" && !event.shiftKey) {
        const message = document.querySelector(".entry-input").value;
        sendMessage("8O", message);
    }
});

window.onload = () => {
    // document.querySelector(".history").scrollTop = document.querySelector(".history").scrollHeight;
    document.querySelector(".entry-input").focus();
}

async function refreshMessages(longPolling) {
    const startTime = new Date();
    const response = await fetch(longPolling ? `get_messages_long.php` : `get_messages_instant.php`);
    const endTime = new Date();
    const data = await response.json();
    console.log(`Refreshed messages in ${endTime - startTime}ms`);
    document.querySelector(".fakeScroll__content").innerHTML = "";
    data.forEach(message => {
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
    // document.querySelector(".history").scrollTop = document.querySelector(".history").scrollHeight;
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