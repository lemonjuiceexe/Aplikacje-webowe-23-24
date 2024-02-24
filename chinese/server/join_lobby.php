<?php
session_set_cookie_params(["SameSite" => "None"]);
session_start();
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

if(isset($_SESSION["session"])) {
    echo json_encode($_SESSION["session"]);
    return;
}

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
    $lobbyObj = new Lobby();
    $lobbyObj->players = $lobbyData->players;
    $lobbyObj->gameState = $lobbyData->gameState;
    $lobbyObj->colorsAvailable = $lobbyData->colorsAvailable;
    $lobbyObj->id = $lobby["id"];

    if(count($lobbyObj->players) < 4 && $lobbyObj->gameState == null)
    {
        $lobbyObj->addPlayer($player);
        $connection->query("UPDATE lobbies SET lobby='".json_encode($lobbyObj)."' WHERE id=".$lobby["id"]);
        $lobbyFound = true;
        $lobbyJoined = $lobbyObj;
        $_SESSION["session"] = array("player" => $playerName, "lobby" => $lobbyObj);
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
    $_SESSION["session"] = array("player" => $playerName, "lobby" => $newLobby);
}
$connection->close();

// http_response_code(200);
$response = array("player" => $player, "lobby" => $lobbyJoined);
echo json_encode($response);
