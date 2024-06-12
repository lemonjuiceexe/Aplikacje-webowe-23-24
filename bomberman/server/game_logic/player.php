<?php

class Player{
    public $discriminator = "player";
    public $id;
    public $x_px;
    public $y_px;
    public $x;
    public $y;
    public $direction;
    public $animation_frame;

    public function __construct($id, $x, $y, $direction){
        $this->id = $id;
        $this->x = $x;
        $this->y = $y;
        $this->x_px = $x * 20;
        $this->y_px = $y * 20;
        $this->direction = $direction;
        $this->animation_frame = 0;
    }

    public function move($direction, $board){
        if(!$this->check_if_move_legal($direction, $board)){
            echo "ILLEGAL MOVE\n";
            return;
        }
        // Animation
        if($this->direction == $direction){
            $this->animation_frame++;
            if($this->animation_frame > 2){
                $this->animation_frame = 0;
            }
        }else{
            $this->animation_frame = 0;
        }

        // echo "Player $this->id moved $direction\n";
        $this->direction = $direction;
        switch ($direction) {
            case Direction::Up:
                $this->y_px -= 5;
                break;
            case Direction::Right:
                $this->x_px += 5;
                break;
            case Direction::Down:
                $this->y_px += 5;
                break;
            case Direction::Left:
                $this->x_px -= 5;
                break;
        }
        $this->calculate_position_from_px();
    }
    public function calculate_position_from_px(){
        $this->x = floor($this->x_px / 32);
        $this->y = floor($this->y_px / 32);
    }
    public function check_if_move_legal($direction, $board){
        $new_x = $this->x;
        $new_y = $this->y;
        switch ($direction) {
            case Direction::Up:
                $new_y--;
                break;
            case Direction::Right:
                $new_x++;
                break;
            case Direction::Down:
                $new_y++;
                break;
            case Direction::Left:
                $new_x--;
                break;
        }
        if ($new_x < 0 || $new_x >= 27 || $new_y < 0 || $new_y >= 13) {
            return false;
        }
        if ($board[$new_y][$new_x] == Field::Border || $board[$new_y][$new_x] == Field::Obstacle) {
            return false;
        }

        return true;
    }
}