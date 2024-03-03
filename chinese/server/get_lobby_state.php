<?php
include "init.php";

if(!isset($_GET["lobbyId"])){
    http_response_code(400);
    return;
}

$connection = new mysqli("localhost", "chinese", "", "chinese");
$lobby = $connection->query("SELECT * FROM lobbies WHERE id=".$_GET["lobbyId"])->fetch_assoc();
$lobbyData = json_decode($lobby["lobby"]);
$lobbyObj = new Lobby($lobbyData->id, $lobbyData->players, $lobbyData->gameState, $lobbyData->lastWinner, $lobbyData->colorsAvailable);

// on every request, check if the current round should end
if($lobbyObj->gameState != null && $lobbyObj->gameState->roundStartTimestamp != null && 
    time() - $lobbyObj->gameState->roundStartTimestamp > GameState::$roundDuration
){
    $gameStateObj = new GameState($lobbyObj->gameState->redTravelled, $lobbyObj->gameState->blueTravelled, $lobbyObj->gameState->greenTravelled, $lobbyObj->gameState->yellowTravelled, $lobbyObj->gameState->colorsPlaying, $lobbyObj->gameState->currentTurn, $lobbyObj->gameState->diceValue, $lobbyObj->gameState->roundStartTimestamp);
    $gameStateObj->nextTurn();
    $lobbyObj->gameState = $gameStateObj;
    $connection->query("UPDATE lobbies SET lobby='".json_encode($lobbyObj)."' WHERE id=".$_GET["lobbyId"]);
}

$connection->close();

echo json_encode([
    "players" => $lobbyObj->players, 
    "gameState" => $lobbyObj->gameState, 
    "lastWinner" => $lobbyObj->lastWinner
]);