<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

include "templates/game_state.php";
include "templates/lobby.php";

session_start();
$connection = new mysqli("localhost", "chinese", "", "chinese");

$player = new Player("Player 1");
// data about existing lobbies from the database
$lobbies = $connection->query("SELECT * FROM lobbies");

// look for a lobby with an empty slot
$lobby_found = false;
while($lobby = $lobbies->fetch_assoc()) {
    $lobbyData = json_decode($lobby["lobby"]);
    $lobbyObj = new Lobby();
    $lobbyObj->players = $lobbyData->players;
    $lobbyObj->gameState = $lobbyData->gameState;
    $lobbyObj->colorsAvailable = $lobbyData->colorsAvailable;

    print_r($lobbyObj);
    if(count($lobbyObj->players) < 4 && $lobbyObj->gameState == null) {
	echo "found";
        $lobbyObj->addPlayer($player);
        $connection->query("UPDATE lobbies SET lobby='".json_encode($lobbyObj)."' WHERE id=".$lobby["id"]);
        $lobby_found = true;
        break;
    }
}
if(!$lobby_found) {
	echo "lobbi";
    // create a new lobby
    $newLobby = new Lobby();
	echo "asd";
    $newLobby->addPlayer($player);
	echo "hhe";
	$connection->query("INSERT INTO lobbies (lobby) VALUES ('".json_encode($newLobby)."')");
}
echo "halo";
$lobbies = $connection->query("SELECT * FROM lobbies");
while($lobby = $lobbies->fetch_assoc()) {
    $lobbyObj = json_decode($lobby["lobby"]);
    echo "<div>";
    echo "<h3>Lobby</h3>";
    echo "<ul>";
    foreach($lobbyObj->players as $player) {
        echo "<li>".$player->name. " - " . $player->color . "</li>";
    }
    echo "</ul>";
    echo "</div>";
}
