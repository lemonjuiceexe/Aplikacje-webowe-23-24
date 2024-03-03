<?php
class Player
{
    public $name = "";
    public $secret = "";
    public $color = Color::UNSET;
    public $isReady = false;

    function __construct($name)
    {
        $this->name = $name;
        $this->secret = md5($name . time() . rand());
    }
}
class Lobby
{
    public $id = 0;
    public $players = [];
    public $gameState = null;
    public $lastWinner = null;
    public $colorsAvailable = [Color::RED, Color::BLUE, Color::GREEN, Color::YELLOW];

    function __construct($id = 0, $players = [], $gameState = null, $lastWinner = null, $colorsAvailable = [Color::RED, Color::BLUE, Color::GREEN, Color::YELLOW])
    {
        $this->id = $id;
        $this->players = $players;
        $this->gameState = $gameState;
        $this->lastWinner = $lastWinner;
        $this->colorsAvailable = $colorsAvailable;
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
    function startGameIfReady()
    {
        $allReady = true;
        foreach ($this->players as $player) {
            if (!$player->isReady) {
                $allReady = false;
                break;
            }
        }
        // start if all are ready or all 4 players joined, don't start with 0 or 1 player
        if (count($this->players) <= 1 || (!$allReady && count($this->players) < 4)) {
            return false;
        }

        // start the game
        $this->gameState = new GameState();
        $this->gameState->colorsPlaying = array_map(function ($player) {
            return $player->color;
        }, $this->players);
        $this->gameState->currentTurn = $this->gameState->colorsPlaying[0];
        $this->gameState->roundStartTimestamp = time();
        return true;
    }
}
