<?php
class SocketServer
{
    private $host;
    private $port;
    private $server;
    private $clients;
    private $game_manager;
    private $players;

    private $tick_interval = 0.02; // seconds
    private $last_tick_time;

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
        $this->last_tick_time = microtime(true);
    }

    public function run()
    {
        $write = NULL;
        $except = NULL;

        while (true) {
            $changed = $this->clients;
            stream_select($changed, $write, $except, 0, 10000); // 10 ms timeout for message processing

            // Handle incoming messages
            $this->handleIncomingMessages($changed);

            // Handle tick updates
            $current_time = microtime(true);
            if ($current_time - $this->last_tick_time >= $this->tick_interval) {
                $this->handleTick();
                $this->last_tick_time = $current_time;
            }

            // Limit the loop execution to avoid high CPU usage
            usleep(10000); // 10 ms delay to prevent high CPU usage
        }
        fclose($this->server);
    }

    private function handleIncomingMessages($changed)
    {
        if (in_array($this->server, $changed)) {
            $client = @stream_socket_accept($this->server);
            if ($client) {
                $new_client_id = uniqid();
                $this->clients[] = $client;
                $this->players[$new_client_id] = $client;
                $this->game_manager->spawn_player($new_client_id);

                stream_set_blocking($client, true);
                $headers = fread($client, 1500);
                $this->handshake($client, $headers, $this->host, $this->port);
                stream_set_blocking($client, false);

                // Initial message after connecting
                $data = json_encode([
                    "id" => $new_client_id,
                    "board" => $this->game_manager->board,
                    "balloons" => $this->getBalloonsData()
                ]);

                $this->send_message($this->clients, $this->mask($data));
            }
            $found_socket = array_search($this->server, $changed);
            unset($changed[$found_socket]);
        }

        foreach ($changed as $changed_socket) {
            $ip = stream_socket_get_name($changed_socket, true);
            $buffer = stream_get_contents($changed_socket);

            if ($buffer === false) {
                echo "Client Disconnected from $ip\n";
                @fclose($changed_socket);
                $found_socket = array_search($changed_socket, $this->clients);
                unset($this->clients[$found_socket]);
                foreach ($this->players as $player_id => $player) {
                    if ($player == $changed_socket) {
                        $this->game_manager->despawn_player($player_id);
                        unset($this->players[$player_id]);
                    }
                }
            } else {
                $unmasked = $this->unmask($buffer);

                // Process the player's move
                foreach ($this->players as $player_id => $player) {
                    if ($player == $changed_socket) {
                        if (isset(json_decode($unmasked)->key)) {
                            $this->game_manager->move_player($player_id, json_decode($unmasked)->key);
                        }

                        // Send the updated board and player ID to all clients
                        $data = json_encode([
                            "id" => $player_id,
                            "board" => $this->game_manager->board,
                            "balloons" => $this->getBalloonsData()
                        ]);
                        $this->send_message($this->clients, $this->mask($data));
                    }
                }
            }
        }
    }

    private function handleTick()
    {
        // print_r($this->game_manager->board);
        // Move balloons every tick
        foreach ($this->game_manager->balloons as $balloon) {
            $balloon->move($this->game_manager->board);
        }
        foreach($this->game_manager->garlics as $garlic){
            $garlic->move($this->game_manager->board);
        }
        $this->game_manager->update_board();

        // Send the current board and balloon states to all clients every tick
        $data = json_encode([
            "board" => $this->game_manager->board,
            "balloons" => $this->getBalloonsData()
        ]);
        $this->send_message($this->clients, $this->mask($data));
    }

    private function getBalloonsData()
    {
        return array_map(function ($balloon) {
            return [
                'x' => $balloon->x,
                'y' => $balloon->y,
                'direction' => $balloon->direction,
                'last_horizontal_direction' => $balloon->last_horizontal_direction,
                'move_percentage' => $balloon->move_percentage
            ];
        }, $this->game_manager->balloons);
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
