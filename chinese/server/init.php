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
    exit;
}
