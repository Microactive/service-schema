<?php

namespace Micronative\ServiceSchema\Tests\Config;

use Micronative\ServiceSchema\Config\EventRegister;
use Micronative\ServiceSchema\Config\Exception\ConfigException;
use phpDocumentor\Reflection\Types\Void_;
use PHPUnit\Framework\TestCase;

class EventRegisterTest extends TestCase
{
    /** @var string */
    protected $testDir;

    /** @var EventRegister $eventRegister */
    protected $eventRegister;

    /**
     * @coversDefaultClass \Micronative\ServiceSchema\Config\EventRegister
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->eventRegister = new EventRegister([$this->testDir . "/assets/configs/events.json"]);
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\EventRegister::loadEvents
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exception\ConfigException
     */
    public function testLoadEventsWithEmptyConfigs()
    {
        $this->eventRegister->setConfigs(null);
        $this->eventRegister->loadEvents();
        $this->assertEquals([], $this->eventRegister->getEvents());
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\EventRegister::loadEvents
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exception\ConfigException
     */
    public function testLoadEventsWithUnsupportedFiles()
    {
        $this->eventRegister->setConfigs([$this->testDir . "/assets/configs/events.csv"]);
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage(ConfigException::UNSUPPORTED_FILE_FORMAT . 'csv');
        $this->eventRegister->loadEvents();
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\EventRegister::loadEvents
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exception\ConfigException
     */
    public function testLoadEvents()
    {
        $this->eventRegister->loadEvents();
        $events = $this->eventRegister->getEvents();
        $this->assertTrue(is_array($events));
        $this->assertTrue(isset($events["Users.afterSaveCommit.Create"]));
        $this->assertTrue(isset($events["Users.afterSaveCommit.Update"]));
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\EventRegister::registerEvent
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exception\ConfigException
     */
    public function testRegisterEvent()
    {
        $this->eventRegister->loadEvents();
        $this->eventRegister->registerEvent("Event.Name", ["FirstServiceClass"]);
        $this->eventRegister->registerEvent("Event.Name", ["SecondServiceClass"]);
        $events = $this->eventRegister->getEvents();

        $this->assertTrue(is_array($events));
        $this->assertTrue(isset($events["Event.Name"]));
        $this->assertEquals(["FirstServiceClass", "SecondServiceClass"], $events["Event.Name"]);
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\EventRegister::retrieveEvent
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exception\ConfigException
     */
    public function testRetrieveEvent()
    {
        $this->eventRegister->loadEvents();
        $this->eventRegister->registerEvent("Event.Name", ["SomeServiceClass"]);
        $event = $this->eventRegister->retrieveEvent("Event.Name");
        $noneExistingEvent = $this->eventRegister->retrieveEvent("Not.Existing.Name");
        $this->assertTrue(is_array($event));
        $this->assertTrue(isset($event["Event.Name"]));
        $this->assertEquals(["SomeServiceClass"], $event["Event.Name"]);
        $this->assertNull($noneExistingEvent);
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\EventRegister::getConfigs
     * @covers \Micronative\ServiceSchema\Config\EventRegister::setConfigs
     * @covers \Micronative\ServiceSchema\Config\EventRegister::getEvents
     * @covers \Micronative\ServiceSchema\Config\EventRegister::setEvents
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exception\ConfigException
     */
    public function testGetterAndSetters()
    {
        $this->eventRegister->loadEvents();
        $this->eventRegister->registerEvent("Event.Name", ["SomeServiceClass"]);

        $entity = $this->eventRegister->getConfigs();
        $this->eventRegister->setConfigs($entity);
        $this->assertSame($entity, $this->eventRegister->getConfigs());

        $entity = $this->eventRegister->getEvents();
        $this->eventRegister->setEvents($entity);
        $this->assertSame($entity, $this->eventRegister->getEvents());
    }
}
