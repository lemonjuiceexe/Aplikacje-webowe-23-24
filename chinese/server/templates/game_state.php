<?php
enum Color: int {
    case UNSET = -1;
    case RED = 1;
    case BLUE = 2;
    case GREEN = 3;
    case YELLOW = 4;
}

class GameState {
    public $redTravelled;
    public $blueTravelled;
    public $greenTravelled;
    public $yellowTravelled;
    public $colorsPlaying;
    public $currentTurn;
    public $diceValue;
    public $roundStartTimestamp;
    public static $roundDuration = 60;

    function __construct ($redTravelled = [0, 0, 0, 0], $blueTravelled = [0, 0, 0, 0], $greenTravelled = [0, 0, 0, 0], $yellowTravelled = [0, 0, 0, 0], 
    $colorsPlaying = [Color::RED, Color::BLUE, Color::GREEN, Color::YELLOW],    
    $currentTurn = Color::RED, $diceValue = null, $roundStartTimestamp = null) {
        $this->redTravelled = $redTravelled;
        $this->blueTravelled = $blueTravelled;
        $this->greenTravelled = $greenTravelled;
        $this->yellowTravelled = $yellowTravelled;
        $this->colorsPlaying = $colorsPlaying;
        $this->currentTurn = $currentTurn;
        $this->diceValue = $diceValue;
        $this->roundStartTimestamp = $roundStartTimestamp;
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
	if(in_array($cellsTraveled + $this->diceValue, [41, 42, 43, 44]) && in_array($cellsTraveled + $this->diceValue, $currentTraveled)){
            return false; // Pawns can't stack on top of each other in their houses
        }
        if($cellsTraveled == 0 && !($this->diceValue == 1 || $this->diceValue == 6)){
            return false; // This pawn is still in the spawn and the dice value is not 1 or 6
        }
        return true;
    }
    // Returns an array of cellsTraveled of pawns that are legal to move with the current dice value
    function getLegalPawns(){
        // this method should not modify the game state
        $legalPawns = [];
        $currentTraveled = match(Color::from($this->currentTurn)) {
            Color::RED => $this->redTravelled,
            Color::BLUE => $this->blueTravelled,
            Color::GREEN => $this->greenTravelled,
            Color::YELLOW => $this->yellowTravelled,
        };
        foreach($currentTraveled as $traveled){
            if($this->isMoveLegal($this->currentTurn, $traveled)){
                $legalPawns[] = $traveled;
            }
        }
        return $legalPawns;
    }
    function movePawn($color, $cellsTraveled){
        function standardiseCellsTraveled($cellsTraveled, $color){
            if($color == 1){
                return $cellsTraveled;
            }
            $offsets = [0, 20, 30, 10];
            $offset = $offsets[$color - 1];
            if ($cellsTraveled + $offset < 41){
                return $cellsTraveled + $offset;
            }
            return $cellsTraveled - (40 - $offset);
        }

        $currentTraveled = match(Color::from($color)) {
            Color::RED => $this->redTravelled,
            Color::BLUE => $this->blueTravelled,
            Color::GREEN => $this->greenTravelled,
            Color::YELLOW => $this->yellowTravelled,
        };
        $newTraveled = [];
        $moved = false; // Only move one pawn, even if there are multiple pawns in the same cell
        $newCell = 0;
        foreach($currentTraveled as $traveled){
            if($traveled == $cellsTraveled && !$moved){
                // move normally unless the pawn is in the spawn
                if($traveled == 0){
                    $newCell = 1;
                } else {
                    $newCell = $traveled + $this->diceValue;
                }
                $newTraveled[] = $newCell;
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

        // check for other pawns in the new cell
        // for each color except the color of the pawn that moved
        $newCell = standardiseCellsTraveled($newCell, $color);
        for($i = 0; $i < 4; $i++){
            if($i + 1 == $color){
                continue;
            }
            $otherTraveled = match(Color::from($i + 1)) {
                Color::RED => $this->redTravelled,
                Color::BLUE => $this->blueTravelled,
                Color::GREEN => $this->greenTravelled,
                Color::YELLOW => $this->yellowTravelled,
            };
            for($j = 0; $j < 4; $j++){
                $otherCell = standardiseCellsTraveled($otherTraveled[$j], $i + 1);
                if($otherCell == $newCell){
                    $otherTraveled[$j] = 0;
                }
            }
            match(Color::from($i + 1)) {
                Color::RED => $this->redTravelled = $otherTraveled,
                Color::BLUE => $this->blueTravelled = $otherTraveled,
                Color::GREEN => $this->greenTravelled = $otherTraveled,
                Color::YELLOW => $this->yellowTravelled = $otherTraveled,
            };
        }

        $this->nextTurn();

        return $this;
    }
    function nextTurn(){
        $index = array_search($this->currentTurn, $this->colorsPlaying);
        $this->currentTurn = $this->colorsPlaying[($index + 1) % count($this->colorsPlaying)];
        $this->diceValue = null;
        $this->roundStartTimestamp = time();
    }

    function checkWin(){
        foreach(
            [$this->redTravelled, $this->blueTravelled, $this->greenTravelled, $this->yellowTravelled] 
            as $index=>$traveled
        ){
            sort($traveled);
            if($traveled == [41, 42, 43, 44]){
                return Color::from($index + 1);
            } 
        }
        return null;
    }
}
