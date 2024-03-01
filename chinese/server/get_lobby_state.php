<?php
include "init.php";

if(!isset($_GET["lobbyId"])){
    http_response_code(400);
    return;
}

$connection = new mysqli("localhost", "chinese", "", "chinese");
$lobby = $connection->query("SELECT * FROM lobbies WHERE id=".$_GET["lobbyId"])->fetch_assoc();
$lobbyData = json_decode($lobby["lobby"]);
$lobbyObj = new Lobby($lobbyData->id, $lobbyData->players, $lobbyData->gameState, $lobbyData->colorsAvailable);
$connection->close();

echo json_encode(["players" => $lobbyObj->players, "gameState" => $lobbyObj->gameState]);