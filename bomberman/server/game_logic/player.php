<?php

class Player {
    public $discriminator = "player";
    public $id;
    public $x_px;
    public $y_px;
    public $x;
    public $y;
    public $direction;
    public $animation_frame;
    public $speed = 2;

    const TILE_SIZE = 32;
    const CENTERING_TOLERANCE = 6; // pixels

    public function __construct($id, $x, $y, $direction) {
        $this->id = $id;
        $this->x = $x;
        $this->y = $y;
        $this->x_px = $x * self::TILE_SIZE;
        $this->y_px = $y * self::TILE_SIZE;
        $this->direction = $direction;
        $this->animation_frame = 0;
    }

    public function move($direction, $board) {
        // Center the player if the direction changes
        if ($this->direction != $direction) {
            $this->snap_to_center();
        }

        if (!$this->check_if_move_legal($direction, $board)) {
            return;
        }

        $this->direction = $direction;

        switch ($direction) {
            case Direction::Up:
                $this->y_px -= $this->speed;
                break;
            case Direction::Right:
                $this->x_px += $this->speed;
                break;
            case Direction::Down:
                $this->y_px += $this->speed;
                break;
            case Direction::Left:
                $this->x_px -= $this->speed;
                break;
        }
        $this->calculate_position_from_px();

        $this->animation_frame = ($this->animation_frame + 1) % 3;
    }

    public function calculate_position_from_px() {
        $this->x = floor($this->x_px / self::TILE_SIZE);
        $this->y = floor($this->y_px / self::TILE_SIZE);
    }

    public function check_if_move_legal($direction, $board) {
        $new_x = $this->x_px;
        $new_y = $this->y_px;
        switch ($direction) {
            case Direction::Up:
                $new_y -= $this->speed;
                break;
            case Direction::Right:
                $new_x += $this->speed;
                break;
            case Direction::Down:
                $new_y += $this->speed;
                break;
            case Direction::Left:
                $new_x -= $this->speed;
                break;
        }

        $new_x_tile = 0; $new_y_tile = 0;
        if($direction == Direction::Down || $direction == Direction::Right){
            $new_x_tile = ceil($new_x / self::TILE_SIZE);
            $new_y_tile = ceil($new_y / self::TILE_SIZE);
        } else {
            $new_x_tile = floor($new_x / self::TILE_SIZE);
            $new_y_tile = floor($new_y / self::TILE_SIZE);
        }

        // Ensure the new position is within the board boundaries
        if ($new_x_tile < 0 || $new_x_tile >= count($board[0]) || $new_y_tile < 0 || $new_y_tile >= count($board)) {
            return false;
        }

        // Check if the position is not a Border or Obstacle
        if ($board[$new_y_tile][$new_x_tile] == Field::Border || $board[$new_y_tile][$new_x_tile] == Field::Obstacle) {
            return false;
        }

        return true;
    }

    private function snap_to_center() {
        // calculate which tile is the closest to the player
        $center_x = $this->x_px + self::TILE_SIZE / 2;
        $center_y = $this->y_px + self::TILE_SIZE / 2;

        $center_x_tile = floor($center_x / self::TILE_SIZE);
        $center_y_tile = floor($center_y / self::TILE_SIZE);

        $center_x_px = $center_x_tile * self::TILE_SIZE;
        $center_y_px = $center_y_tile * self::TILE_SIZE;

        $this->x_px = $center_x_px;
        $this->y_px = $center_y_px;

        $this->calculate_position_from_px();
    }
}
