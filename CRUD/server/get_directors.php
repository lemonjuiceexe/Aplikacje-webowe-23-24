<?php

Header("Access-Control-Allow-Origin: http://localhost:5173");

$connection = mysqli_connect('localhost', 'root', '', 'movies');
if ($connection->connect_error) {
	die("Connection failed: " . $connection->connect_error);
}

$result = $connection->query("SELECT * FROM directors")->fetch_all(MYSQLI_ASSOC);
echo json_encode($result);
$connection->close();
