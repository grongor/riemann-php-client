<?php

namespace Riemann;

use DrSlump\Protobuf;

class Client
{

    /**
     * @var Event[]
     */
    private $events;

    /**
     * @var Socket
     */
    private $socket;

    /**
     * @param Socket $socket
     */
    public function __construct(Socket $socket)
    {
        $this->socket = $socket;
    }

    /**
     * @param Event $event
     */
    public function sendEvent(Event $event)
    {
        $queue = $this->events;
        $this->events = [];

        $this->queueEvent($event);
        $this->flush();

        $this->events = $queue;
    }

    /**
     * @param Event $event
     */
    public function queueEvent(Event $event)
    {
        $this->events[] = $event;
    }

    public function flush()
    {
        if (!$this->events) {
            return;
        }

        $message = new Msg();
        $message->ok = true;
        $message->events = $this->events;

        $this->socket->write(Protobuf::encode($message));

        $this->events = [];
    }
}
