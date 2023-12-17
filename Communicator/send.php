<?php
$_POST = json_decode(file_get_contents("php://input"), true);

$memcache = new Memcache();
$memcache->connect('localhost', 11211) or die ("Could not connect");
$current_messages = $memcache->get("messages") ? $memcache->get("messages") : [];

if(!isset($_POST["message"]))
    return false;

$current_messages[] = [
    "user" => $_POST["user"],
    "message" => $_POST["message"],
    "timestamp" => time()
];

$memcache->set("messages", $current_messages);

echo json_encode($current_messages);

?>