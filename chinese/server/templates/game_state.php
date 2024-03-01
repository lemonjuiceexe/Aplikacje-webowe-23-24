<?php
enum Color: int {
    case UNSET = -1;
    case RED = 1;
    case BLUE = 2;
    case GREEN = 3;
    case YELLOW = 4;
}

class GameState {
    public $redTravelled = [0, 0, 0, 0];
    public $blueTravelled = [0, 0, 0, 0];
    public $greenTravelled = [0, 0, 0, 0];
    public $yellowTravelled = [0, 0, 0, 0];
    public $currentTurn = Color::RED;
    public $diceValue = 0;

    function __construct ($redTravelled = [0, 7, 0, 0], $blueTravelled = [0, 0, 0, 0], $greenTravelled = [0, 0, 0, 0], $yellowTravelled = [0, 0, 0, 0], 
        $currentTurn = Color::RED, 
        $diceValue = 0) {
        $this->redTravelled = $redTravelled;
        $this->blueTravelled = $blueTravelled;
        $this->greenTravelled = $greenTravelled;
        $this->yellowTravelled = $yellowTravelled;
        $this->currentTurn = $currentTurn;
        $this->diceValue = $diceValue;
    }

    function isMoveLegal($color, $cellsTraveled){
        $currentTraveled = match(Color::from($color)) {
            Color::RED => $this->redTravelled,
            Color::BLUE => $this->blueTravelled,
            Color::GREEN => $this->greenTravelled,
            Color::YELLOW => $this->yellowTravelled,
        };
        if(!in_array($cellsTraveled, $currentTraveled)){
            return false; // This pawn doesn't exist in the current game state
        }
        if(!in_array($this->diceValue, [1, 2, 3, 4, 5, 6])){
            return false; // This dice value is invalid
        }
        if($cellsTraveled + $this->diceValue > 44){
            return false; // This pawn would move past the end of the board
        }
        return true;
    }
    function movePawn($color, $cellsTraveled){
        $currentTraveled = match(Color::from($color)) {
            Color::RED => $this->redTravelled,
            Color::BLUE => $this->blueTravelled,
            Color::GREEN => $this->greenTravelled,
            Color::YELLOW => $this->yellowTravelled,
        };
        $newTraveled = [];
        $moved = false; // Only move one pawn, even if there are multiple pawns in the same cell
        foreach($currentTraveled as $traveled){
            if($traveled == $cellsTraveled && !$moved){
                $newTraveled[] = $traveled + $this->diceValue;
                $moved = true;
            } else {
                $newTraveled[] = $traveled;
            }
        }
        match(Color::from($color)) {
            Color::RED => $this->redTravelled = $newTraveled,
            Color::BLUE => $this->blueTravelled = $newTraveled,
            Color::GREEN => $this->greenTravelled = $newTraveled,
            Color::YELLOW => $this->yellowTravelled = $newTraveled,
        };
    }
}