<?php
include "init.php";

if(!isset($_POST["playerSecret"]) || !isset($_POST["lobbyId"])) {
    http_response_code(400);
    return;
}

$connection = new mysqli("localhost", "chinese", "", "chinese");
// check if there's a player with a matching secret in the lobby
$playerSecret = $_POST["playerSecret"];
$lobbyId = $_POST["lobbyId"];
$lobby = $connection->query("SELECT * FROM lobbies WHERE id=".$lobbyId)->fetch_assoc();
$lobbyData = json_decode($lobby["lobby"]);
$lobbyObj = new Lobby($lobby["id"], $lobbyData->players, $lobbyData->gameState, $lobbyData->lastWinner, $lobbyData->colorsAvailable);

foreach($lobbyObj->players as $player) {
    if($player->secret == $playerSecret) {
        $roll = rand(1, 6);
        $lobbyObj->gameState->diceValue = $roll;
        $connection->query("UPDATE lobbies SET lobby='".json_encode($lobbyObj)."' WHERE id=".$lobbyId);
        echo json_encode($roll);
        $connection->close();
        return;
    }
}

http_response_code(403);
$connection->close();