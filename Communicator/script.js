const availableColors = ["tomato", "orange", "gold", "limegreen", "lightgreen", "deepskyblue", "mediumorchid", "deeppink"];
const joinTimestamp = new Date();
let refreshing = false;
let username = "";
let color = availableColors[Math.floor(Math.random() * availableColors.length)];

window.onload = () => {
    document.querySelector(".fakeScroll__content").scrollTop = document.querySelector(".fakeScroll__content").scrollHeight;
    document.querySelector(".entry-input").focus();
    document.querySelector(".history").fakeScroll({});
    username = prompt("Enter your username:");
    if (!username) {
        username = "Anonymous";
    }
    sendMessage("System", "lightgreen", `${username} joined the chat.`);
}

// Send message on button click or enter key press
document.querySelector(".entry-submit").addEventListener("click", () => {
    const message = document.querySelector(".entry-input").value;
    sendMessage(username, color, message);
});
document.addEventListener("keydown", event => {
    if (event.key === "Enter" && !event.shiftKey) {
        const message = document.querySelector(".entry-input").value;
        sendMessage(username, color, message);
    }
});

async function refreshMessages() {
    const startTime = new Date();
    const response = await fetch(`get_messages_long.php`);
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
                            <p class="message-author" style="color: ${message.color}">${message.user}</p>
                            <p class="message-timestamp">${message.formatted_timestamp}</p>
                        </div>
                        <p class="message-content">${message.message}</p>
                    </div>
                    `;
    });
    $(".message-content").emoticonize();
    document.querySelector(".fakeScroll__content").scrollTop = document.querySelector(".fakeScroll__content").scrollHeight;
}
function sendMessage(user, color, message) {
    if (!message || !user || !availableColors.includes(color)) {
        return;
    }
    if(message[0] === "/") {
        parseCommand(message);
        return;
    }
    console.log(`Sending message: ${message}`);
    fetch(`send.php`, {
        method: "POST",
        body: JSON.stringify({
            user: user,
            message: message,
            color: color
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
function parseCommand(message){
    const command = message.split(" ")[0];
    const args = message.split(" ").slice(1);
    switch(command){
        case "/help":
            sendMessage("System", "lightgreen", 
            `Available commands: /help, /name, /color, /quit <br> 
            Available colors: ${availableColors.join(", ")}`);
            break;
        case "/name":
            const oldUsername = username;
            username = args.join(" ");
            console.log('new username', username);
            sendMessage("System", "lightgreen", `${oldUsername} username changed to ${username}`);
            break;
        case "/color":
            if(availableColors.includes(args[0])){
                color = args[0];
                sendMessage("System", "lightgreen", `${username} color changed to ${color}`);
            }
            else{
                sendMessage("System", "lightgreen", `Invalid color.`);
            }
            break;
        case "/quit":
            sendMessage("System", "lightgreen", `${username} left the chat.`);
            window.location.href = "index.php";
            break;
        default:
            sendMessage("System", "lightgreen", "Unknown command.");
    }
}