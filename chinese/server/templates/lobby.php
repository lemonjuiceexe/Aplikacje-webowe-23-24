<?php
class Player
{
    public $name = "";
    // public $sessionId = ""; ??
    public $color = Color::UNSET;
    public $isReady = false;

    function __construct($name)
    {
        $this->name = $name;
    }
}
class Lobby
{
    public $players = [];
    public $gameState = null;
    public $colorsAvailable = [Color::RED, Color::BLUE, Color::GREEN, Color::YELLOW];

    function __construct()
    {
        $this->players = [];
        $this->gameState = null;
    }
    function addPlayer($player)
    {
        if (count($this->players) < 4) {
            // randomly assign a color to the player
            $color = $this->colorsAvailable[array_rand(($this->colorsAvailable))];
            $player->color = $color;
            // remove the assigned color from the available colors
            $index = array_search($color, $this->colorsAvailable);
            if ($index !== FALSE) {
                unset($this->colorsAvailable[$index]);
                $this->colorsAvailable = array_values($this->colorsAvailable);
            }
            array_push($this->players, $player);
        }
    }

}
