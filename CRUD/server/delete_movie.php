<?php

Header("Access-Control-Allow-Origin: http://localhost:5173");

$connection = mysqli_connect('localhost', 'root', '', 'movies');
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$id = $_GET['id'];

$connection->query("DELETE FROM movies WHERE id = $id");
$connection->close();