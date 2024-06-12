<?php
require_once("types.php");
require_once("balloon.php");

class GameManager
{
    public $board = array();
    public $balloons = array();
    public $max_balloon_count = 15;

    public function initialise_board()
    {
        for ($i = 0; $i < 13; $i++) {
            $this->board[$i] = array();
            for ($j = 0; $j < 27; $j++) {
                // Board borders
                if ($i == 0 || $i == 12 || $j == 0 || $j == 26) {
                    $this->board[$i][$j] = Field::Border;
                }
                // Central unbreakable walls
                else if ($i % 2 == 0 && $j % 2 == 0) {
                    $this->board[$i][$j] = Field::Border;
                } else {
                    if (count($this->balloons) < $this->max_balloon_count && rand(0, 100) < 10) {
                        $balloon = new Balloon($j, $i, rand(0, 3));
                        $this->balloons[] = $balloon;
                        $this->board[$i][$j] = $balloon;
                    } else {
                        $this->board[$i][$j] = Field::Empty;
                    }
                }
            }
        }
        // Randomly fill empty spaces with obstacles
        for ($i = 0; $i < 13; $i++) {
            for ($j = 0; $j < 27; $j++) {
                if (
                    $this->board[$i][$j] == Field::Empty && rand(0, 100) < 20
                    // Make sure the player can move freely at the start
                    && ($i > 4 || $j > 4)
                ) {
                    $this->board[$i][$j] = Field::Obstacle;
                }
            }
        }
        $this->board[1][1] = Field::Player;
    }

    public function update_board()
    {
        // Remove all balloons from the board
        for ($i = 0; $i < 13; $i++) {
            for ($j = 0; $j < 27; $j++) {
                if ($this->board[$i][$j] instanceof Balloon) {
                    $this->board[$i][$j] = Field::Empty;
                }
            }
        }
        // Add balloons to the board
        foreach ($this->balloons as $balloon) {
            $this->board[$balloon->y][$balloon->x] = $balloon;
            echo "Balloon at $balloon->x, $balloon->y\n";
        }
    }
}
