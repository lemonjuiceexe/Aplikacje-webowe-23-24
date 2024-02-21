<?php
include "templates/game_state.php";
include "templates/lobby.php";

session_start();

$player = new Player("Player 1");
// data about existing lobbies from the database
if(isset($_SESSION["lobbies"])) {
    $lobbies = $_SESSION["lobbies"];
} else {
    $lobbies = [];
}

$found_lobby = false;

foreach ($lobbies as $lobby) {
    // print_r($lobby->players);
    // echo "<br>";
    // if the game has already started or the lobby is full, skip it
    if($lobby->gameState != null || count($lobby->players) == 4) {
        continue;
    }
    // if the lobby is not full, add the player to the lobby
    $lobby->addPlayer($player);
    $found_lobby = true;
}
// // if no lobby was found, create a new one
if(!$found_lobby) {
    $lobby = new Lobby();
    $lobby->addPlayer($player);
    array_push($lobbies, $lobby);
}

echo "<br><br>Lobbies: <br>";
echo(count($lobbies));

foreach($lobbies as $lobby) {
    echo "<br>";
    foreach($lobby->players as $player) {
        echo $player->name . " " . $player->color . "<br>";
    }
}

$_SESSION["lobbies"] = $lobbies;