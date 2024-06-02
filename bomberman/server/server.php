<?php
class Field {
	const Empty = "-";
	const Border = "*";
	const Obstacle = "X";
	const Player = "P";
	const Enemy = "E";
}

class GameManager {
	public $board = array();

	public function initialise_board(){
		for($i = 0; $i < 13; $i++){
			$this->board[$i] = array();
			for($j = 0; $j < 31; $j++){
                // Board borders
				if($i == 0 || $i == 12 || $j == 0 || $j == 30){
					$this->board[$i][$j] = Field::Border;
				}
                // Central unbreakable walls
                else if ($i % 2 == 0 && $j % 2 == 0){
                    $this->board[$i][$j] = Field::Border;
                }
                else{
					$this->board[$i][$j] = Field::Empty;
				}
			}
		}
	}
}

class SocketServer {
    private $host;
    private $port;
    private $server;
    private $clients;
    private $game_manager;

    private $tick_time = 1;

    public function __construct($host, $port, $game_manager) {
        $this->host = $host;
        $this->port = $port;
        $this->game_manager = $game_manager;
        $this->server = stream_socket_server("tcp://$this->host:$this->port", $errno, $errstr);
        if (!$this->server) {
            die("$errstr ($errno)");
        }
        $this->clients = array($this->server);
    }

    public function run() {
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
                $this->clients[] = $client;
                $ip = stream_socket_get_name($client, true);
                echo "New Client connected from $ip\n";

                stream_set_blocking($client, true);
                $headers = fread($client, 1500);
                $this->handshake($client, $headers, $this->host, $this->port);
                stream_set_blocking($client, false);

				// Initial message after connecting
//                 $data=["message" => "Connection with server estabilished"];
                $this->send_message($this->clients, $this->mask(
                	json_encode($this->game_manager->board)
                ));

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
            }

			// Send the board to all clients every tick
            $this->send_message($this->clients, $this->mask(
                	json_encode($this->game_manager->board)
            ));
        }
        fclose($this->server);
    }

    private function unmask($text) {
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
    private function mask($text) {
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
    private function handshake($client, $rcvd, $host, $port) {
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
        $upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "WebSocket-Origin: $host\r\n" .
            "WebSocket-Location: ssl://$host:$port\r\n" .
            "Sec-WebSocket-Version: 13\r\n".
            "Sec-WebSocket-Accept:$secAccept\r\n\r\n";
        fwrite($client, $upgrade);
    }

    private function send_message($clients, $msg) {
        foreach ($clients as $changed_socket) {
            @fwrite($changed_socket, $msg);
        }
    }
}

$game_manager = new GameManager();
$game_manager->initialise_board();

$socketServer = new SocketServer('127.0.0.1', 46089, $game_manager);
$socketServer->run();