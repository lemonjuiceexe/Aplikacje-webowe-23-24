<?php
class Player {
    public $name = "";
    // public $sessionId = ""; ??
    public $color = -1;
    public $isReady = false;

    function __construct($name) {
        $this->name = $name;
    }
}
class Lobby {
    public $players = [];
    public $gameState = null;
    public $colorsAvailable = [0, 1, 2, 3];

    function __construct() {
        $this->players = [];
        $this->gameState = null;
    }
    function addPlayer($player) {
        if(count($this->players) < 4) {
            // randomly assign a color to the player
            $player->color = $this->colorsAvailable[array_rand($this->colorsAvailable)];
            // remove the color from the available colors
            $this->colorsAvailable = array_diff($this->colorsAvailable, [$player->color]);
            array_push($this->players, $player);
        }
    }

}