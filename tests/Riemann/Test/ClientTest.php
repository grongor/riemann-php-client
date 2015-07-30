<?php
namespace Riemann\Test;

use DrSlump\Protobuf;
use Riemann\Client;
use Riemann\Event;
use Riemann\Msg;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    const A_HOST = 'localhost';
    const A_PORT = 5555;

    /**
     * @test
     */
    public function itShouldNotSendAnything()
    {
        $socketClient = $this->socketClientMock();
        $socketClient->expects($this->never())
            ->method('write');
        $client = new Client($socketClient, $this->eventBuilderFactoryMock());
        $client->flush();
    }

    /**
     * @test
     */
    public function itShouldSendEvents()
    {
        $anEvent = new Event();
        $anotherEvent = new Event();
        $message = $this->aMessage(
            array(
                $anEvent,
                $anotherEvent,
            )
        );

        $socketClient = $this->socketClientMock();
        $socketClient->expects($this->once())
            ->method('write')
            ->with(Protobuf::encode($message));
        $client = new Client($socketClient, $this->eventBuilderFactoryMock());
        $client->addEvent($anEvent);
        $client->addEvent($anotherEvent);
        $client->flush();
    }

    /**
     * @test
     */
    public function itShouldReturnANewEventBuilder()
    {
        $eventBuilder = $this->eventBuilderMock();
        $eventBuilderFactory = $this->eventBuilderFactoryMock();
        $eventBuilderFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($eventBuilder));
        $client = new Client($this->socketClientMock(), $eventBuilderFactory);

        $this->assertThat(
            $client->getEventBuilder(),
            $this->equalTo($eventBuilder)
        );
    }

    private function socketClientMock()
    {
        return $this->getMockBuilder('Riemann\Socket')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function aMessage($events = array())
    {
        $message = new Msg();
        $message->ok = true;
        $message->events = $events;
        return $message;
    }

    private function eventBuilderFactoryMock()
    {
        return $this->getMockBuilder('Riemann\EventBuilderFactory')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function eventBuilderMock()
    {
        return $this->getMockBuilder('Riemann\EventBuilder')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
