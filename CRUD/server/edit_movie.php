<?php

Header("Access-Control-Allow-Origin: http://localhost:5173");

$connection = mysqli_connect('localhost', 'root', '', 'movies');
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$id = $_POST['id'];
$title = $_POST['title'];
$year = $_POST['year'];
$length = $_POST['length'];
$director_id = $_POST['director_id'];
$count = $_POST['count'];
$rating = $_POST['rating'];

$connection->query("UPDATE movies SET title = '$title', year = '$year', length = '$length', director_id = '$director_id', count = '$count', rating = '$rating' WHERE id = $id");
$connection->close();

// the new http standard success status code
echo ":)";
