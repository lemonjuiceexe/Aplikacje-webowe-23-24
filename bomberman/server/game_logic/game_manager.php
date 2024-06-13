<?php
require_once("types.php");
require_once("enemy.php");
require_once("player.php");

class GameManager
{
    public $board = array();
    public $balloons = array();
    public $garlics = array();
    public $players = array();
    public $max_balloon_count = 15;
    public $max_garlic_count = 1;

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
                elseif ($i % 2 == 0 && $j % 2 == 0) {
                    $this->board[$i][$j] = Field::Border;
                } else {
                    if (count($this->balloons) < $this->max_balloon_count && rand(0, 100) < 10) {
                        $balloon = new Balloon($j, $i, rand(0, 3));
                        $this->balloons[] = $balloon;
                        $this->board[$i][$j] = $balloon;
                    }
                    else if (count($this->garlics) < $this->max_garlic_count && rand(0, 100) < 10) {
                        $garlic = new Garlic($j, $i, rand(0, 3));
                        $this->garlics[] = $garlic;
                        $this->board[$i][$j] = $garlic;
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
    }

    public function update_board()
    {
        // Remove all balloons from the board
        for ($i = 0; $i < 13; $i++) {
            for ($j = 0; $j < 27; $j++) {
                if ($this->board[$i][$j] instanceof Balloon || $this->board[$i][$j] instanceof Garlic || $this->board[$i][$j] instanceof Player) {
                    $this->board[$i][$j] = Field::Empty;
                }
            }
        }
        // Add balloons to the board
        foreach ($this->balloons as $balloon) {
            $this->board[$balloon->y][$balloon->x] = $balloon;
        }
        foreach ($this->garlics as $garlic) {
            $this->board[$garlic->y][$garlic->x] = $garlic;
        }
        foreach ($this->players as $player) {
            $this->board[$player->y][$player->x] = $player;
        }
    }

    public function spawn_player($id){
        $player = new Player($id, 0, 0, Direction::Down);
        $player->id = $id;
        // Find empty spot for player
        do {
            $player->x = rand(0, 26);
            $player->y = rand(0, 12);
        } while ($this->board[$player->y][$player->x] != Field::Empty);

        $player->x_px = $player->x * 32;
        $player->y_px = $player->y * 32;

        $this->players[] = $player;
        $this->board[$player->y][$player->x] = $player;
    }
    public function despawn_player($id){
        foreach ($this->players as $key => $player) {
            if($player->id == $id){
                $this->board[$player->y][$player->x] = Field::Empty;
                unset($this->players[$key]);
                break;
            }
        }
    }
    public function move_player($id, $key){
        $direction = null;
        switch ($key) {
            case "ArrowUp":
                $direction = Direction::Up;
                break;
            case "ArrowRight":
                $direction = Direction::Right;
                break;
            case "ArrowDown":
                $direction = Direction::Down;
                break;
            case "ArrowLeft":
                $direction = Direction::Left;
                break;
        }

        foreach ($this->players as $key => $player) {
            if($player->id == $id){
                $player->move($direction, $this->board);
                break;
            }
        }
        $this->update_players();

    }
    public function update_players(){
        for ($i = 0; $i < 13; $i++) {
            for ($j = 0; $j < 27; $j++) {
                if (is_object($this->board[$i][$j]) && $this->board[$i][$j]->discriminator == "player") {
                    $this->board[$i][$j] = Field::Empty;
                }
            }
        }
        foreach ($this->players as $player) {
            if($this->board[$player->y][$player->x] == Field::Empty){
                $this->board[$player->y][$player->x] = $player;
            }
        }
    }
}
