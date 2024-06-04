<?php
class Field
{
    const Empty = "-";
    const Border = "*";
    const Obstacle = "X";
    const Player = "P";
}
class Direction
{
    const Up = 0;
    const Right = 1;
    const Down = 2;
    const Left = 3;
}

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

        if(in_array($direction, [Direction::Up, Direction::Down])) {
            $this->last_horizontal_direction = Direction::Right;
        } else {
            $this->last_horizontal_direction = $this->direction;
        }
    }
    public function move($board) {
        echo "My direction is $this->direction\n";
        if($this->move_percentage == 100)
            $this->move_percentage = 0;
        if($this->move_percentage == 0){
            $can_move = false;
            $new_x = $this->x;
            $new_y = $this->y;

            switch ($this->direction) {
                case Direction::Up:
                    $new_x -= 1;
                    break;
                case Direction::Right:
                    $new_y += 1;
                    break;
                case Direction::Down:
                    $new_x += 1;
                    break;
                case Direction::Left:
                    $new_y -= 1;
                    break;
            }

            if ($board[$new_x][$new_y] == Field::Empty || $board[$new_x][$new_y] == Field::Player) {
                $can_move = true;
            }

            if ($can_move && rand(0, 100) < 80) {
                $this->x = $new_x;
                $this->y = $new_y;
                echo "Balloon moved to $this->x, $this->y\n";
            } else {
                $possible_directions = [];

                foreach ([Direction::Up, Direction::Right, Direction::Down, Direction::Left] as $dir) {
                    $test_x = $this->x;
                    $test_y = $this->y;

                    switch ($dir) {
                        case Direction::Up:
                            $test_x -= 1;
                            break;
                        case Direction::Right:
                            $test_y += 1;
                            break;
                        case Direction::Down:
                            $test_x += 1;
                            break;
                        case Direction::Left:
                            $test_y -= 1;
                            break;
                    }

                    if ($board[$test_x][$test_y] == Field::Empty || $board[$test_x][$test_y] == Field::Player) {
                        $possible_directions[] = $dir;
                    }
                }

                if (!empty($possible_directions)) {
                    $this->direction = $possible_directions[array_rand($possible_directions)];
                }
            }
        } else{
            $this->move_percentage += 10;
        }
    }
}

class GameManager
{
    public $board = array();
    public $balloons = array();

    public function initialise_board()
    {
        for ($i = 0; $i < 13; $i++) {
            $this->board[$i] = array();
            for ($j = 0; $j < 31; $j++) {
                // Board borders
                if ($i == 0 || $i == 12 || $j == 0 || $j == 30) {
                    $this->board[$i][$j] = Field::Border;
                }
                // Central unbreakable walls
                else if ($i % 2 == 0 && $j % 2 == 0) {
                    $this->board[$i][$j] = Field::Border;
                } else {
                    // Temporarily only spawn one balloon
                    if($i == 3 && $j == 3){
                        $balloon = new Balloon($i, $j, rand(0, 3));
                        $this->balloons[] = $balloon;
                        $this->board[$i][$j] = $balloon;
                    } else{
                        $this->board[$i][$j] = Field::Empty;
                    }
                    // // Randomly spawn baloons in empty spaces
                    // if (rand(0, 100) < 10) {
                    //     $this->board[$i][$j] = new Balloon($i, $j, rand(0, 3));
                    // } else {
                    //     $this->board[$i][$j] = Field::Empty;
                    // }
                }
            }
        }
        // Randomly fill empty spaces with obstacles
        for ($i = 0; $i < 13; $i++) {
            for ($j = 0; $j < 31; $j++) {
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
    public function update_board(){
        //remove all balloons from the board
        for ($i = 0; $i < 13; $i++) {
            for ($j = 0; $j < 31; $j++) {
                if($this->board[$i][$j] instanceof Balloon){
                    $this->board[$i][$j] = Field::Empty;
                }
            }
        }
        foreach ($this->balloons as $balloon) {
            $this->board[$balloon->y][$balloon->x] = $balloon;
            echo "Balloon at $balloon->x, $balloon->y\n";
        }
    }
}

class SocketServer
{
    private $host;
    private $port;
    private $server;
    private $clients;
    private $game_manager;
    private $players;

    private $tick_time = 1;

    public function __construct($host, $port, $game_manager)
    {
        $this->host = $host;
        $this->port = $port;
        $this->game_manager = $game_manager;
        $this->server = stream_socket_server("tcp://$this->host:$this->port", $errno, $errstr);
        if (!$this->server) {
            die("$errstr ($errno)");
        }
        $this->clients = array($this->server);
        $this->players = array();
    }

    public function run()
    {
        $write = NULL;
        $except = NULL;

        while (true) {
            $changed = $this->clients;
            stream_select($changed, $write, $except, $this->tick_time);

            if (in_array($this->server, $changed)) {
                $client = @stream_socket_accept($this->server);
                if (!$client) {
                    continue;
                }

                $new_client_id = uniqid();
                $this->clients[] = $client;
                $this->players[$new_client_id] = $client;

                stream_set_blocking($client, true);
                $headers = fread($client, 1500);
                $this->handshake($client, $headers, $this->host, $this->port);
                stream_set_blocking($client, false);

                // Initial message after connecting
                $data = json_encode([
                    "id" => $new_client_id,
                    "board" => $this->game_manager->board
                ]);

                $this->send_message(
                    $this->clients,
                    $this->mask(
                        $data
                    )
                );

                $found_socket = array_search($this->server, $changed);
                unset($changed[$found_socket]);
            }

            foreach ($changed as $changed_socket) {
                $ip = stream_socket_get_name($changed_socket, true);
                $buffer = stream_get_contents($changed_socket);

                if ($buffer == false) {
                    echo "Client Disconnected from $ip\n";
                    @fclose($changed_socket);
                    $found_socket = array_search($changed_socket, $this->clients);
                    unset($this->clients[$found_socket]);
                }

                $unmasked = $this->unmask($buffer);
                if ($unmasked != "") {
                    echo "\nReceived a Message from $ip:\n\"$unmasked\" \n";
                }

                $response = $this->mask($unmasked);
                $this->send_message($this->clients, $response);

                // Send the current board and client ID to all clients every tick
                foreach ($this->players as $player_id => $player) {
                    if ($player == $changed_socket) {
                        echo "Player $player_id moved\n";

                        $data = json_encode([
                            "id" => $player_id,
                            "board" => $this->game_manager->board
                        ]);
                        $this->send_message(
                            $this->clients,
                            $this->mask(
                                $data
                            )
                        );
                    }
                }
            }

            // Move balloons every tick
            echo "There are " . count($this->game_manager->balloons) . " balloons\n";
            foreach ($this->game_manager->balloons as $balloon) {
                $balloon->move($this->game_manager->board);
            }
            $this->game_manager->update_board();

            // Send the current board to all clients every tick
            $data = json_encode([
                "board" => $this->game_manager->board
            ]);
            $this->send_message(
                $this->clients,
                $this->mask($data)
            );
        }
        fclose($this->server);
    }

    private function unmask($text)
    {
        $length = @ord($text[1]) & 127;
        if ($length == 126) {
            $masks = substr($text, 4, 4);
            $data = substr($text, 8);
        } elseif ($length == 127) {
            $masks = substr($text, 10, 4);
            $data = substr($text, 14);
        } else {
            $masks = substr($text, 2, 4);
            $data = substr($text, 6);
        }
        $text = "";
        for ($i = 0; $i < strlen($data); ++$i) {
            $text .= $data[$i] ^ $masks[$i % 4];
        }
        return $text;
    }
    private function mask($text)
    {
        $b1 = 0x80 | (0x1 & 0x0f);
        $length = strlen($text);
        if ($length <= 125)
            $header = pack('CC', $b1, $length);
        elseif ($length > 125 && $length < 65536)
            $header = pack('CCn', $b1, 126, $length);
        elseif ($length >= 65536)
            $header = pack('CCNN', $b1, 127, $length);
        return $header . $text;
    }
    private function handshake($client, $rcvd, $host, $port)
    {
        $headers = array();
        $lines = preg_split("/\r\n/", $rcvd);
        foreach ($lines as $line) {
            $line = rtrim($line);
            if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
                $headers[$matches[1]] = $matches[2];
            }
        }
        $secKey = $headers['Sec-WebSocket-Key'];
        $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
        $upgrade = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "WebSocket-Origin: $host\r\n" .
            "WebSocket-Location: ssl://$host:$port\r\n" .
            "Sec-WebSocket-Version: 13\r\n" .
            "Sec-WebSocket-Accept:$secAccept\r\n\r\n";
        fwrite($client, $upgrade);
    }

    private function send_message($clients, $msg)
    {
        foreach ($clients as $changed_socket) {
            @fwrite($changed_socket, $msg);
        }
    }
}

$game_manager = new GameManager();
$game_manager->initialise_board();

$socketServer = new SocketServer('127.0.0.1', 46089, $game_manager);
$socketServer->run();