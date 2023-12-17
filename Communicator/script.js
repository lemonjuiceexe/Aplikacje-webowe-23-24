// Send message on button click or enter key press
document.querySelector(".entry-submit").addEventListener("click", sendMessage);
document.addEventListener("keydown", event => {
    if (event.key === "Enter" && !event.shiftKey) {
        sendMessage();
    }
});

window.onload = () => {
    document.querySelector(".history").scrollTop = document.querySelector(".history").scrollHeight;
    document.querySelector(".entry-input").focus();
}

function sendMessage() {
    const message = document.querySelector(".entry-input").value;
    if (!message) {
        return;
    }
    fetch(`send.php`, {
        method: "POST",
        body: JSON.stringify({
            user: "test-user",
            message: message
        }),
        headers: {
            "Content-Type": "application/json",
        }
    })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            window.location.reload();
        });
    document.querySelector(".entry-input").value = "";
}