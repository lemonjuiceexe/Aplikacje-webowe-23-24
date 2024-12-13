<?php
// Update the database
$value;
if(array_key_exists("illness", $_GET)){
    $value = -1;
}
else if(array_key_exists("no_data", $_GET)){
    $value = null;
}
else{
    $value = $_GET["value"];
}

$conn = new mysqli("127.0.0.1", "root", "", "graph");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("UPDATE data SET value = ? WHERE argument = ?;");
$stmt->bind_param("ii", $value, $_GET["id"]);
$stmt->execute();
$stmt->close();
$conn->close();

// Redirect to index.php
header("Location: index.php");