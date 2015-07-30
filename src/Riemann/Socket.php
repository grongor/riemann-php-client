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
     * @param string $host
     * @param int    $port
     * @param bool   $persistent
     */
    public function __construct($host, $port, $persistent)
    {
        $this->host = $host;
        $this->port = $port;
        $this->persistent = $persistent;
    }

    /**
     * @param string $data
     *
     * @throws \Exception
     */
    public function write($data)
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
