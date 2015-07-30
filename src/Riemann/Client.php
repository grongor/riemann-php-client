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
     * @var EventBuilderFactory
     */
    private $eventBuilderFactory;

    /**
     * @param Socket              $socket
     * @param EventBuilderFactory $eventBuilderFactory
     */
    public function __construct(Socket $socket, EventBuilderFactory $eventBuilderFactory)
    {
        $this->socket = $socket;
        $this->eventBuilderFactory = $eventBuilderFactory;
    }

    /**
     * @param string $host
     * @param int    $port
     * @param bool   $persistent
     *
     * @return Client
     */
    public static function create($host, $port, $persistent = false)
    {
        return new self(new Socket($host, $port, $persistent), new EventBuilderFactory());
    }

    /**
     * @return EventBuilder
     */
    public function getEventBuilder()
    {
        return $this->eventBuilderFactory->create();
    }

    /**
     * @param Event $event
     */
    public function addEvent(Event $event)
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
