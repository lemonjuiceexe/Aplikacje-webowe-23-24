<?php
require_once("types.php");

class Balloon
{
    public $x;
    public $y;
    public $direction;
    public $last_horizontal_direction;
    public $move_percentage;

    public function __construct($x, $y, $direction)
    {
        $this->x = $x;
        $this->y = $y;
        $this->direction = $direction;
        $this->move_percentage = 0;

        if (in_array($direction, [Direction::Up, Direction::Down])) {
            $this->last_horizontal_direction = Direction::Right;
        } else {
            $this->last_horizontal_direction = $this->direction;
        }
    }

    public function calculate_position_after_move($direction)
    {
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
        return [$new_x, $new_y];
    }

    public function is_legal_move($direction, $board)
    {
        list($new_x, $new_y) = $this->calculate_position_after_move($direction);

        // Ensure the new position is within the board boundaries
        if ($new_x < 0 || $new_x >= count($board[0]) || $new_y < 0 || $new_y >= count($board)) {
            return false;
        }

        // Check if the position is not a Border or Obstacle
        if ($board[$new_y][$new_x] == Field::Border || $board[$new_y][$new_x] == Field::Obstacle) {
            return false;
        }

        return true;
    }

    public function move($board)
    {
        if($this->move_percentage >= 100){
            $this->move_percentage = 0;
        } else {
            $this->move_percentage += 20;
        }
        if ($this->move_percentage == 0) {
            if ($this->is_legal_move($this->direction, $board) && rand(0, 100) < 80) {
                list($new_x, $new_y) = $this->calculate_position_after_move($this->direction);
                $this->x = $new_x;
                $this->y = $new_y;
            } else {
                $possible_directions = [];

                foreach ([Direction::Up, Direction::Right, Direction::Down, Direction::Left] as $dir) {
                    if ($this->is_legal_move($dir, $board)) {
                        $possible_directions[] = $dir;
                    }
                }

                if (!empty($possible_directions)) {
                    $this->direction = $possible_directions[array_rand($possible_directions)];
                }
            }
        }
    }
}