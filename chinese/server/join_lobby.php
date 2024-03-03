<?php
include "init.php";

$connection = new mysqli("localhost", "chinese", "", "chinese");
$playerName = $_GET["playerName"] ?? "Player";
$player = new Player($playerName);
// data about existing lobbies from the database
$lobbies = $connection->query("SELECT * FROM lobbies");

// look for a lobby with an empty slot
$lobbyFound = false;
$lobbyJoined;
while($lobby = $lobbies->fetch_assoc()) {
    $lobbyData = json_decode($lobby["lobby"]);
    $lobbyObj = new Lobby($lobby["id"], $lobbyData->players, $lobbyData->gameState, $lobbyData->lastWinner, $lobbyData->colorsAvailable);

    if(count($lobbyObj->players) < 4 && $lobbyObj->gameState == null)
    {
        $lobbyObj->addPlayer($player);
        // start the game if needed
        $gameStarted = $lobbyObj->startGameIfReady();
        $connection->query("UPDATE lobbies SET lobby='".json_encode($lobbyObj)."' WHERE id=".$lobby["id"]);
        $lobbyFound = true;
        $lobbyJoined = $lobbyObj;
        break;
    }
}
if(!$lobbyFound) {
    // create a new lobby
    $newLobby = new Lobby();
    $newLobby->addPlayer($player);
	$connection->query("INSERT INTO lobbies (lobby) VALUES ('".json_encode($newLobby)."')");
    $id = $connection->query("SELECT LAST_INSERT_ID()")->fetch_assoc()["LAST_INSERT_ID()"];
    $newLobby->id = $id;
    $lobbyJoined = $newLobby;
}

$connection->close();

//FIXME: Can't just return all players with their secrets
$response = array("player" => $player, "lobby" => $lobbyJoined);
echo json_encode($response);
