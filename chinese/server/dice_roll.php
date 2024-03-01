<?php
include "templates/game_state.php";
include "templates/lobby.php";

Header("Access-Control-Allow-Origin: http://localhost:4200");
Header("Access-Control-Allow-Credentials: true");
Header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Origin, Access-Control-Allow-Headers, Access-Control-Allow-Credentials");
Header("Content-Type: application/json");

// if the request is a preflight request, just return the headers
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    return;
}

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
$lobbyObj = new Lobby();
$lobbyObj->players = $lobbyData->players;
$lobbyObj->gameState = $lobbyData->gameState;
$lobbyObj->colorsAvailable = $lobbyData->colorsAvailable;
$lobbyObj->id = $lobby["id"];

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