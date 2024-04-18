<?php

Header("Access-Control-Allow-Origin: http://localhost:5173");

$connection = mysqli_connect('localhost', 'root', '', 'movies');
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$name = $_POST['name'];
$movie_count = $_POST['movie_count'];

$connection->query("INSERT INTO directors (name, movie_count) VALUES ('$name', '$movie_count')");
$connection->close();

echo json_encode($director);