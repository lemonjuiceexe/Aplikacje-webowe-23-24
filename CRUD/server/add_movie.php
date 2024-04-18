<?php

Header("Access-Control-Allow-Origin: http://localhost:5173");

$connection = mysqli_connect('localhost', 'root', '', 'movies');
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$title = $_POST['title'];
$year = $_POST['year'];
$length = $_POST['length'];
$director_id = $_POST['director_id'];
$count = $_POST['count'];
$rating = $_POST['rating'];

$connection->query("INSERT INTO movies (title, year, length, director_id, count, rating) VALUES ('$title', '$year', '$length', '$director_id', '$count', '$rating')");
$connection->close();

