<?php
$_POST = json_decode(file_get_contents("php://input"), true);

if(!isset($_POST["message"]))
    return false;

// $memcache = new Memcache();
// $memcache->connect('localhost', 11211) or die ("Could not connect");
// $messages_on_request = $memcache->get("messages") ? $memcache->get("messages") : [];

$conn = mysqli_connect("localhost", "root", "", "communicator");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$now = time();
$conn->query("
    INSERT INTO `messages` (`user`, `message`, `timestamp`, `color`) 
    VALUES ('{$_POST["user"]}', '{$_POST["message"]}', now(), '{$_POST["color"]}')");

$conn->close();

die();

?>