<?php
require_once("socket_server.php");
require_once("game_logic/game_manager.php");


$game_manager = new GameManager();
$game_manager->initialise_board();

$socketServer = new SocketServer('127.0.0.1', 46089, $game_manager);
$socketServer->run();