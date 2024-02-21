<?php
enum Color: int {
    case RED = 0;
    case BLUE = 1;
    case GREEN = 2;
    case YELLOW = 3;
}

class GameState {
    public $redTravelled = [0, 0, 0, 0];
    public $blueTravelled = [0, 0, 0, 0];
    public $greenTravelled = [0, 0, 0, 0];
    public $yellowTravelled = [0, 0, 0, 0];
    public $currentTurn = Color::RED;
    public $diceValue = 0;

    function __construct() {
        $this->redTravelled = [0, 0, 0, 0];
        $this->blueTravelled = [0, 0, 0, 0];
        $this->greenTravelled = [0, 0, 0, 0];
        $this->yellowTravelled = [0, 0, 0, 0];
        $this->currentTurn = Color::RED;
        $this->diceValue = 0;
    }
}