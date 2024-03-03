<?php
include "init.php";

if (!isset($_POST["playerSecret"]) || !isset($_POST["lobbyId"])){
    var_dump($_POST);
    return;
}

$playerSecret = $_POST["playerSecret"];
$lobbyId = $_POST["lobbyId"];

$connection = new mysqli("localhost", "chinese", "", "chinese");
$lobby = $connection->query("SELECT * FROM lobbies WHERE id=".$lobbyId)->fetch_assoc();
$lobbyData = json_decode($lobby["lobby"]);
$lobbyObj = new Lobby($lobby["id"], $lobbyData->players, $lobbyData->gameState, $lobbyData->lastWinner, $lobbyData->colorsAvailable);

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