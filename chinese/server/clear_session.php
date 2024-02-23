<?php
session_start();
session_destroy();
$connection = new mysqli("localhost", "chinese", "", "chinese");
$connection->query("DELETE FROM lobbies");
$connection->close();
