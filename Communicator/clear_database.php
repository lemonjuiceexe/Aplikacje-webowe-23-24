<?php
// $memcache = new Memcache();
// $memcache->connect('localhost', 11211) or die ("Could not connect");
// $memcache->flush(); 
$conn = mysqli_connect("localhost", "root", "", "communicator");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$conn->query("TRUNCATE TABLE `messages`");

$conn->close();

?>