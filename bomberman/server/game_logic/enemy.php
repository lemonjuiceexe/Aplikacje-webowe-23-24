<?php
require_once ("types.php");

class Balloon
{
    public $discriminator = "balloon";
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

    public function calculate_position_after_move($direction, $x = null, $y = null)
    {
        $new_x = $x === null ? $this->x : $x;
        $new_y = $y === null ? $this->y : $y;

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


    function choose_new_direction($board)
    {
        $possible_directions = [];
        foreach ([Direction::Up, Direction::Right, Direction::Down, Direction::Left] as $dir) {
            if ($this->is_legal_move($dir, $board)) {
                $possible_directions[] = $dir;
            }
        }

        if (!empty($possible_directions)) {
            $this->direction = $possible_directions[array_rand($possible_directions)];
        } else {
            $this->direction = null;
        }

        if (in_array($this->direction, [Direction::Right, Direction::Left])) {
            $this->last_horizontal_direction = $this->direction;
        }
    }
    public function move($board)
    {
        // check if move in the current direction is possible
        if (!$this->is_legal_move($this->direction, $board)) {
            $this->choose_new_direction($board);
        }
        // if there's still no legal direction, don't move 
        if (!$this->is_legal_move($this->direction, $board)) {
            return;
        }

        $this->move_percentage += 2;
        if ($this->move_percentage >= 100) {
            list($new_x, $new_y) = $this->calculate_position_after_move($this->direction);
            $this->y = $new_y;
            $this->x = $new_x;
            if ($this->is_legal_move($this->direction, $board) && rand(0, 100) < 80) {
            } else {
                $this->choose_new_direction($board);
            }

            $this->move_percentage = 0;
        }
    }
}


class Garlic extends Balloon
{
    public $discriminator = "garlic";

    public function __construct($x, $y, $direction)
    {
        parent::__construct($x, $y, $direction);
    }

    private function calculate_direction($start_x, $start_y, $target_x, $target_y)
    {
        if ($start_x == $target_x && $start_y == $target_y) {
            return -1;
        }

        if ($start_x < $target_x) {
            echo "Right\n";
            return Direction::Right;
        } elseif ($start_x > $target_x) {
            echo "Left\n";
            return Direction::Left;
        } elseif ($start_y < $target_y) {
            echo "Down\n";
            return Direction::Down;
        } elseif ($start_y > $target_y) {
            echo "Up\n";
            return Direction::Up;
        }
    }

    function findShortestPathDirection($board, $start_x, $start_y)
    {
        $queue = new SplQueue(); // Queue for BFS
        $visited = []; // To track visited cells
        $directions = [[-1, 0], [0, 1], [1, 0], [0, -1]]; // Up, Right, Down, Left

        // Initialize visited array for all board cells
        foreach ($board as $row) {
            $visitedRow = [];
            foreach ($row as $cell) {
                $visitedRow[] = false; // Initialize all cells as not visited
            }
            $visited[] = $visitedRow;
        }

        // Structure to store parent cell and direction
        $parent = [];
        foreach ($board as $row) {
            $parentRow = [];
            foreach ($row as $cell) {
                $parentRow[] = null;
            }
            $parent[] = $parentRow;
        }

        $queue->enqueue([$start_x, $start_y]);
        $visited[$start_y][$start_x] = true;

        $foundPlayer = false;
        $playerX = -1;
        $playerY = -1;

        // Perform BFS to find the nearest 'player'
        while (!$queue->isEmpty()) {
            list($x, $y) = $queue->dequeue();

            // Check if the target ('player') is found
            if (is_object($board[$y][$x]) && $board[$y][$x]->discriminator === 'player') {
                $foundPlayer = true;
                $playerX = $x;
                $playerY = $y;
                break;
            }

            // Explore neighbors
            foreach ($directions as $dir) {
                $new_x = $x + $dir[1];
                $new_y = $y + $dir[0];

                // Check if the new position is within bounds and not visited
                if (
                    $new_x >= 0 && $new_x < count($board[0]) && $new_y >= 0 && $new_y < count($board) &&
                    !$visited[$new_y][$new_x] && $board[$new_y][$new_x] !== Field::Border &&
                    $board[$new_y][$new_x] !== Field::Obstacle
                ) {

                    $visited[$new_y][$new_x] = true;
                    $parent[$new_y][$new_x] = [$x, $y];
                    $queue->enqueue([$new_x, $new_y]);
                }
            }
        }

        // Backtrack from player's position to find the starting point of the shortest path
        $currentX = $playerX;
        $currentY = $playerY;

        while ($parent[$currentY][$currentX] !== null && ($parent[$currentY][$currentX][0] !== $start_x || $parent[$currentY][$currentX][1] !== $start_y)) {
            list($parentX, $parentY) = $parent[$currentY][$currentX];
            $currentX = $parentX;
            $currentY = $parentY;
        }

        // Determine direction to move from start to the beginning of the shortest path
        return $this->calculate_direction($start_x, $start_y, $currentX, $currentY);
    }

    public function choose_new_direction($board)
    {
        // list($player_x, $player_y) = $this->find_player_coords($board);
        // echo "Player coords: $player_x, $player_y\n";
        // if ($player_x !== null && $player_y !== null) {
        $direction = $this->findShortestPathDirection($board, $this->x, $this->y);
        if ($direction != null && $direction != -1) {
            echo "Chose: $direction\n";
            $this->direction = $direction;
        } else {
            parent::choose_new_direction($board);
        }
        // }
    }

    public function move($board)
    {
        if(!$this->is_legal_move($this->direction, $board)){
            $this->choose_new_direction($board);
        }
        // After finishing every move, find the player and calculate the new direction
        $this->move_percentage += 2;
        if ($this->move_percentage >= 100) {
            list($new_x, $new_y) = $this->calculate_position_after_move($this->direction);
            $this->y = $new_y;
            $this->x = $new_x;
            $this->move_percentage = 0;
            $this->choose_new_direction($board);

            echo "Going to: $this->direction\n";
        }
    }
}