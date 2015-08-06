<?php

namespace Riemann;

class Socket
{

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var bool
     */
    private $persistent;

    /**
     * @var bool
     */
    private $useTCP;

    /**
     * @param string $host
     * @param int $port
     * @param bool $persistent
     * @param bool $useTCP
     */
    public function __construct($host = 'localhost', $port = 5555, $persistent = false, $useTCP = true)
    {
        $this->host = $host;
        $this->port = $port;
        $this->persistent = $persistent;
        $this->useTCP = $useTCP;
    }

    /**
     * @param string $data
     */
    public function write($data)
    {
        if ($this->useTCP) {
            $this->writeTCP($data);
        } else {
            $this->writeUDP($data);
        }
    }

    /**
     * @param string $data
     */
    private function writeTCP($data)
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            echo "socket_create() failed: reason: " .
                socket_strerror(socket_last_error()) . "\n";
            exit;
        }

        $result = socket_connect($socket, $this->host, $this->port);
        if ($result === false) {
            echo "socket_connect() failed.\nReason: ($result) " .
                socket_strerror(socket_last_error($socket)) . "\n";
        }

        socket_write($socket, pack('N', strlen($data)));
        socket_write($socket, $data);
        socket_close($socket);
    }

    /**
     * @param string $data
     *
     * @throws \Exception
     */
    private function writeUDP($data)
    {
        $flags = STREAM_CLIENT_CONNECT;
        if ($this->persistent) {
            $flags |= STREAM_CLIENT_PERSISTENT;
        }

        $fp = stream_socket_client(sprintf('udp://%s:%d', $this->host, $this->port), $errNo, $err, null, $flags);
        if (!$fp) {
            throw new \Exception(sprintf('Failed to create client socket: [%d] %s', $errNo, $err));
        }

        fwrite($fp, $data);
        fclose($fp);
    }

}
