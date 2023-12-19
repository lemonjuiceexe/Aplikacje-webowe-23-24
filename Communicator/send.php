<?php
$_POST = json_decode(file_get_contents("php://input"), true);

$memcache = new Memcache();
$memcache->connect('localhost', 11211) or die ("Could not connect");
$messages_on_request = $memcache->get("messages") ? $memcache->get("messages") : [];

if(!isset($_POST["message"]))
    return false;

$now = time();
$messages_on_request[] = [
    "user" => $_POST["user"],
    "message" => $_POST["message"],
    "timestamp" => $now,
    "formatted_timestamp" => date("d/m/y H:i", $now)
];

$memcache->set("messages", $messages_on_request);

echo json_encode($messages_on_request);
die();

?>