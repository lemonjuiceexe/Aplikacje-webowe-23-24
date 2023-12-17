document.querySelector(".entry-submit").addEventListener("click", () => {
    const message = document.querySelector(".entry-input").value;
    sendMessage(message)
        .then(response => response.json())
        .then(data => {
            console.log(data);
        });
});

function sendMessage(message){
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