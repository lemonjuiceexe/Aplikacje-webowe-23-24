<?php
$padding = 100;
$dot_size = 8;

function fetchFromDatabase(
    $host = "127.0.0.1",
    $username = "root",
    $password = "",
    $database = "graph"
    ){
    global $data, $x_name, $y_name, $border_value;
    $connection = new mysqli($host, $username, $password, $database);
    $result = $connection->query("SELECT * FROM data");
    while($row = $result->fetch_assoc())
        $data[] = [$row["argument"], $row["value"]];
    $x_name = $connection->query("SELECT value FROM metadata WHERE name = 'x_name'")->fetch_assoc()["value"];
    $y_name = $connection->query("SELECT value FROM metadata WHERE name = 'y_name'")->fetch_assoc()["value"];
    $border_value = $connection->query("SELECT value FROM metadata WHERE name = 'border_value'")->fetch_assoc()["value"];

    $connection->close();

    return [
        "data" => $data,
        "x_name" => $x_name,
        "y_name" => $y_name,
        "border_value" => $border_value
    ];
}