<?php
include "templates/game_state.php";
include "templates/lobby.php";

Header("Access-Control-Allow-Origin: http://localhost:4200");
Header("Access-Control-Allow-Credentials: true");
Header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Origin, Access-Control-Allow-Headers, Access-Control-Allow-Credentials");
Header("Content-Type: multipart/form-data");

// if the request is a preflight request, just return the headers
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    return;
}
if (!isset($_POST["playerSecret"]) || !isset($_POST["lobbyId"])){
    var_dump($_POST);
    return;
}

$playerSecret = $_POST["playerSecret"];
$lobbyId = $_POST["lobbyId"];

$connection = new mysqli("localhost", "chinese", "", "chinese");

$lobby = $connection->query("SELECT * FROM lobbies WHERE id=".$lobbyId)->fetch_assoc();
$lobbyData = json_decode($lobby["lobby"]);
$lobbyObj = new Lobby();
$lobbyObj->players = $lobbyData->players;
$lobbyObj->gameState = $lobbyData->gameState;
$lobbyObj->colorsAvailable = $lobbyData->colorsAvailable;
$lobbyObj->id = $lobby["id"];

$playerFound = false;
foreach($lobbyObj->players as $player) {
    if($player->secret == $playerSecret) {
        $player->isReady = !$player->isReady;
        $playerFound = true;
        $connection->query("UPDATE lobbies SET lobby='".json_encode($lobbyObj)."' WHERE id=".$lobby["id"]);
        break;
    }
}
if(!$playerFound) {
    http_response_code(400);
    return;
}

// start the game if needed
$gameStarted = $lobbyObj->startGameIfReady();
if($gameStarted) {
    $connection->query("UPDATE lobbies SET lobby='".json_encode($lobbyObj)."' WHERE id=".$lobby["id"]);
}

$connection->close();

$response = array("lobby" => $lobbyObj);
echo json_encode($response);