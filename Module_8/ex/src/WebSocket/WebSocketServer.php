<?php
// src/WebSocket/WebSocketServer.php
namespace App\WebSocket;

use Socket;

class WebSocketServer
{
    /** @var Socket[] */
    private array $clients = [];
    private Socket $socket;

    public function start(int $port = 8081): void
    {
        // Création et configuration du socket
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind($this->socket, '0.0.0.0', $port);
        socket_listen($this->socket);

        echo "✅ WebSocket Server démarré sur le port $port...\n";

        while (true) {
            $read = [$this->socket];
            $write = $except = null;

            foreach ($this->clients as $client) {
                $read[] = $client;
            }

            if (socket_select($read, $write, $except, 0) < 1) {
                continue;
            }

            // Accepter une nouvelle connexion
            if (in_array($this->socket, $read, true)) {
                $client = socket_accept($this->socket);
                if ($client) {
                    $this->clients[] = $client;
                    $this->handshake($client);
                    echo "🔗 Nouvelle connexion WebSocket !\n";
                }
                continue;
            }

            // Lire les messages des clients
            foreach ($read as $client) {
                if ($client !== $this->socket) {
                    $data = $this->receive($client);

                    if ($data === false) {
                        $this->disconnect($client);
                        continue;
                    }

                    echo "📩 Message reçu : " . json_encode($data) . "\n";

                    if (isset($data['type']) && $data['type'] === 'post.created') {
                        echo "📢 Diffusion d'un nouveau post...\n";
                    }

                    $this->broadcast(json_encode($data));
                }
            }
        }
    }

    /**
     * @param Socket $client
     */
    private function handshake(Socket $client): void
    {
        $request = socket_read($client, 1024);
        preg_match('#Sec-WebSocket-Key: (.*)\r\n#', $request, $matches);

        if (!isset($matches[1])) {
            error_log("❌ Impossible de récupérer la clé WebSocket.");
            return;
        }

        $key = base64_encode(pack(
            'H*',
            sha1($matches[1] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')
        ));

        $headers = implode("\r\n", [
            "HTTP/1.1 101 Switching Protocols",
            "Upgrade: websocket",
            "Connection: Upgrade",
            "Sec-WebSocket-Accept: $key",
            "\r\n"
        ]);

        socket_write($client, $headers, strlen($headers));
        error_log("✅ Handshake réussi avec un client WebSocket.");
    }

    private function receive(Socket $client)
    {
        $data = socket_read($client, 2048, PHP_BINARY_READ);
    
        if ($data === false) {
            error_log("❌ Erreur lecture socket");
            return false;
        }
    
        if ($data === '') {
            error_log("⚠️ Données vides reçues");
            return false;
        }
    
        try {
            $decodedMessage = $this->decode($data);
            error_log("Message décodé : " . $decodedMessage);
    
            $jsonData = json_decode($decodedMessage, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("❌ Erreur JSON : " . json_last_error_msg());
                return false;
            }
    
            error_log("✅ Message JSON valide reçu : " . json_encode($jsonData));
            return $jsonData;
        } catch (\Exception $e) {
            error_log("❌ Erreur décodage : " . $e->getMessage());
            return false;
        }
    }

    private function decode(string $data): string
    {
        $firstByte = ord($data[0]);
        $secondByte = ord($data[1]);
        
        $fin = (bool)($firstByte & 0x80);
        $opcode = $firstByte & 0x0F;
        $isMasked = (bool)($secondByte & 0x80);
        $payloadLength = $secondByte & 0x7F;
        
        $offset = 2;
        
        if ($payloadLength === 126) {
            $payloadLength = unpack('n', substr($data, $offset, 2))[1];
            $offset += 2;
        } elseif ($payloadLength === 127) {
            $payloadLength = unpack('J', substr($data, $offset, 8))[1];
            $offset += 8;
        }
        
        if ($isMasked) {
            $maskingKey = substr($data, $offset, 4);
            $offset += 4;
        }
        
        $payload = substr($data, $offset);
        
        if ($isMasked) {
            $unmasked = '';
            for ($i = 0; $i < strlen($payload); $i++) {
                $unmasked .= $payload[$i] ^ $maskingKey[$i % 4];
            }
            return $unmasked;
        }
        
        return $payload;
    }
    
    private function encode(string $message): string
    {
        $messageLength = strlen($message);
        
        // Premier octet: FIN = 1, RSV1-3 = 0, Opcode = 0x1 (text)
        $frame = chr(0x81);
        
        // Second octet: MASK = 0, Payload len = ?
        if ($messageLength <= 125) {
            $frame .= chr($messageLength);
        } elseif ($messageLength <= 65535) {
            $frame .= chr(126) . pack('n', $messageLength);
        } else {
            $frame .= chr(127) . pack('J', $messageLength);
        }
        
        return $frame . $message;
    }
    
    private function broadcast(string $message): void
    {
        error_log("Broadcasting: " . $message);
        
        $encoded = $this->encode($message);
        foreach ($this->clients as $client) {
            $written = socket_write($client, $encoded);
            if ($written === false) {
                error_log("❌ Échec d'envoi au client");
                $this->disconnect($client);
            }
        }
    }

    /**
     * @param Socket $client
     */
    private function disconnect(Socket $client): void
    {
        // Vérification avant fermeture
        if (@socket_close($client) === false) {
            error_log("⚠️ Erreur lors de la fermeture du socket client.");
        }

        // Suppression propre du client
        $index = array_search($client, $this->clients, true);
        if ($index !== false) {
            unset($this->clients[$index]);
            error_log("✅ Client déconnecté avec succès.");
        } else {
            error_log("⚠️ Client non trouvé dans la liste.");
        }
    }
}