document.querySelector(".entry-submit").addEventListener("click", sendMessage);
document.addEventListener("keydown", event => {
    if (event.key === "Enter" && !event.shiftKey) {
        sendMessage();
    }
});

function sendMessage() {
    const message = document.querySelector(".entry-input").value;
    if (!message) {
        return;
    }
    fetchMessage(message)
        .then(response => response.json())
        .then(data => {
            console.log(data);
            window.location.reload();
        });
    document.querySelector(".entry-input").value = "";
}
function fetchMessage(message){
    return fetch(`send.php`, {
        method: "POST",
        body: JSON.stringify({
            user: "test-user",
            message: message
        }),
        headers: {
            "Content-Type": "application/json",
        }
    });
}