<?php
$conn = mysqli_connect("localhost", "root", "", "communicator");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
function get_messages(){
    global $conn;
    $messages = [];
    $result = $conn->query("SELECT * FROM `messages`");
    while($row = $result->fetch_assoc()){
        $messages[] = $row;
    }
    return $messages;
}

// Delete messages older than 5 minutes
$conn->query("DELETE FROM `messages` WHERE `timestamp` < NOW() - INTERVAL 2 MINUTE");

$messages_on_request = get_messages();

$start_time = time();
$duration = 5;

// For 5 seconds, check for new messages every half a second
while(time() < $start_time + $duration){
    usleep(50000);
    if($messages_on_request != get_messages()){
        $messages_on_request = get_messages();
        break;
    }
}

$conn->close();

foreach ($messages_on_request as $key => $message) {
    $messages_on_request[$key]["formatted_timestamp"] = date("d/m/y H:i", strtotime($message["timestamp"]));
}

echo json_encode($messages_on_request);
die();
?>
