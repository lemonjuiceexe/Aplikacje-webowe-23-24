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
}