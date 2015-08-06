<?php

namespace Riemann\Test;

use DrSlump\Protobuf;
use Riemann\Client;
use Riemann\Event;
use Riemann\Msg;
use Riemann\Socket;

class ClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function itShouldNotSendAnything()
    {
        $socketClient = $this->socketClientMock();
        $socketClient->expects($this->never())
            ->method('write');

        $client = new Client($socketClient);
        $client->flush();
    }

    /**
     * @test
     */
    public function itShouldSendEvents()
    {
        $anEvent = new Event();
        $anotherEvent = new Event();
        $message = $this->getMessage([$anEvent, $anotherEvent]);

        $socketClient = $this->socketClientMock();
        $socketClient->expects($this->once())
            ->method('write')
            ->with(Protobuf::encode($message));

        $client = new Client($socketClient);
        $client->queueEvent($anEvent);
        $client->queueEvent($anotherEvent);
        $client->flush();
    }

    /**
     * @test
     */
    public function itShouldSendEventImmediately()
    {
        $anEvent = new Event();
        $anotherEvent = new Event();
        $thirdEvent = new Event();

        $socketClient = $this->socketClientMock();
        $socketClient->expects($this->at(0))
                     ->method('write')
                     ->with(Protobuf::encode($this->getMessage([$thirdEvent])));
        $socketClient->expects($this->at(1))
                     ->method('write')
                     ->with(Protobuf::encode($this->getMessage([$anEvent, $anotherEvent])));

        $client = new Client($socketClient);
        $client->queueEvent($anEvent);
        $client->queueEvent($anotherEvent);
        $client->sendEvent($thirdEvent);
        $client->flush();
    }

    /**
     * @return Socket|\PHPUnit_Framework_MockObject_MockObject
     */
    private function socketClientMock()
    {
        return $this->getMock(Socket::class);
    }

    private function getMessage($events = [])
    {
        $message = new Msg();
        $message->ok = true;
        $message->events = $events;

        return $message;
    }

}
