// Send message on button click or enter key press
document.querySelector(".entry-submit").addEventListener("click", sendMessage);
document.addEventListener("keydown", event => {
    if (event.key === "Enter" && !event.shiftKey) {
        const message = document.querySelector(".entry-input").value;
        sendMessage("8O", message);
    }
});

window.onload = () => {
    document.querySelector(".history").scrollTop = document.querySelector(".history").scrollHeight;
    document.querySelector(".entry-input").focus();

    // Fetch the messages for the first time
    refreshMessages(false);
    // Constantly refresh messages using long polling
    (async () => {
        while (true) {
            await refreshMessages(true);
        }
    })();
}

async function refreshMessages(longPolling) {
    const startTime = new Date();
    const response = await fetch(longPolling ? `get_messages_long.php` : `get_messages_instant.php`);
    const endTime = new Date();
    const data = await response.json();
    console.log(`Refreshed messages in ${endTime - startTime}ms`);
    document.querySelector(".history").innerHTML = "";
    data.forEach(message => {
        document.querySelector(".history").innerHTML += `
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
    document.querySelector(".history").scrollTop = document.querySelector(".history").scrollHeight;

}
function sendMessage(user, message) {
    if (!message) {
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
        .then(response => response.json())
        .then(data => {
            console.log(data);
            refreshMessages();
        });
    document.querySelector(".entry-input").value = "";
}