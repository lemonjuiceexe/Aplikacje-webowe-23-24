<?php
include "init.php";

if (!isset($_POST["playerSecret"]) || !isset($_POST["lobbyId"]) || !isset($_POST["cellsTraveled"])){
    http_response_code(400);
    return;
}

$playerSecret = $_POST["playerSecret"];
$cellsTraveled = $_POST["cellsTraveled"];

$connection = new mysqli("localhost", "chinese", "", "chinese");
$lobby = $connection->query("SELECT * FROM lobbies WHERE id=".$_POST["lobbyId"])->fetch_assoc();
$lobbyData = json_decode($lobby["lobby"]);
$lobbyObj = new Lobby($lobby["id"], $lobbyData->players, $lobbyData->gameState, $lobbyData->colorsAvailable);

foreach($lobbyObj->players as $player) {
    if($player->secret == $playerSecret) {
        //TODO: Uncomment after testing
        // if($lobbyObj->gameState->currentTurn != $player->color) {
        //     http_response_code(403);
        //     echo "It's not $player->color's turn";
        //     return;
        // }

        $gameStateObj = new GameState
            ($lobbyObj->gameState->redTravelled, $lobbyObj->gameState->blueTravelled, $lobbyObj->gameState->greenTravelled, $lobbyObj->gameState->yellowTravelled, 
            $lobbyObj->gameState->currentTurn, $lobbyObj->gameState->diceValue);

        if(!$gameStateObj->isMoveLegal($player->color, $cellsTraveled)){
            http_response_code(403);
            echo "Illegal move";
            $connection->close();
            return;
        }
        $gameStateObj->movePawn($player->color, $cellsTraveled);
        $winner = $gameStateObj->checkWin();
        $winnerIsNull = $winner == null;
        error_log("returned $winnerIsNull");

        $lobbyObj->gameState = $gameStateObj;

        $connection->query("UPDATE lobbies SET lobby='".json_encode($lobbyObj)."' WHERE id=".$lobby["id"]);
        break;
    }
}

echo json_encode($lobbyObj->gameState);
// If there's a winner the game is over.
// Return current game state to the client so it can display the winner.
// Only then set the game state to null so the client will exit to the lobby after the next fetch.
if($winner != null){
    $lobbyObj->gameState = null;
    $connection->query("UPDATE lobbies SET lobby='".json_encode($lobbyObj)."' WHERE id=".$lobby["id"]);
}
$connection->close();
