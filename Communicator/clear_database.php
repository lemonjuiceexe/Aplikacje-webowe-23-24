<?php
$conn = mysqli_connect("localhost", "root", "", "communicator");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$conn->query("TRUNCATE TABLE `messages`");

$conn->close();

?>