<?php
include "init.php";

if(!isset($_GET["lobbyId"])){
    http_response_code(400);
    return;
}

$connection = new mysqli("localhost", "chinese", "", "chinese");
$lobby = $connection->query("SELECT * FROM lobbies WHERE id=".$_GET["lobbyId"])->fetch_assoc();
$lobbyData = json_decode($lobby["lobby"]);
$lobbyObj = new Lobby();
$lobbyObj->gameState = $lobbyData->gameState;
$connection->close();

echo json_encode($lobbyObj->gameState);