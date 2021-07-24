<?php

namespace Micronative\ServiceSchema\Tests\Config;

use Micronative\ServiceSchema\Config\EventRegister;
use Micronative\ServiceSchema\Config\Exceptions\ConfigException;
use PHPUnit\Framework\TestCase;

class EventRegisterTest extends TestCase
{
    /** @coversDefaultClass \Micronative\ServiceSchema\Config\EventConfigRegister */
    protected $eventRegister;

    /** @var string */
    protected $testDir;

    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->eventRegister = new EventRegister([$this->testDir . "/assets/configs/events.json"]);
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\EventRegister::loadEvents
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     */
    public function testLoadEventsWithEmptyConfigs()
    {
        $this->eventRegister->setConfigFiles(null);
        $this->eventRegister->loadEvents();
        $this->assertEquals([], $this->eventRegister->getEventConfigs());
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\EventRegister::loadEvents
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     */
    public function testLoadEventsWithUnsupportedFiles()
    {
        $this->eventRegister->setConfigFiles([$this->testDir . "/assets/configs/events.csv"]);
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage(ConfigException::UNSUPPORTED_FILE_FORMAT . 'csv');
        $this->eventRegister->loadEvents();
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\EventRegister::loadEvents
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     */
    public function testLoadEvents()
    {
        $this->eventRegister->loadEvents();
        $events = $this->eventRegister->getEventConfigs();
        $this->assertTrue(is_array($events));
        $this->assertTrue(isset($events["Users.afterSaveCommit.Create"]));
        $this->assertTrue(isset($events["Users.afterSaveCommit.Update"]));
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\EventRegister::registerEventConfig
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     */
    public function testRegisterEvent()
    {
        $this->eventRegister->loadEvents();
        $this->eventRegister->registerEventConfig("Event.Name", ["FirstServiceClass"]);
        $this->eventRegister->registerEventConfig("Event.Name", ["SecondServiceClass"]);
        $events = $this->eventRegister->getEventConfigs();

        $this->assertTrue(is_array($events));
        $this->assertTrue(isset($events["Event.Name"]));
        $this->assertEquals(["FirstServiceClass", "SecondServiceClass"], $events["Event.Name"]);
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\EventRegister::retrieveEventConfig
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     */
    public function testRetrieveEvent()
    {
        $this->eventRegister->loadEvents();
        $this->eventRegister->registerEventConfig("Event.Name", ["SomeServiceClass"]);
        $event = $this->eventRegister->retrieveEventConfig("Event.Name");
        $noneExistingEvent = $this->eventRegister->retrieveEventConfig("Not.Existing.Name");
        $this->assertTrue(is_array($event));
        $this->assertTrue(isset($event["Event.Name"]));
        $this->assertEquals(["SomeServiceClass"], $event["Event.Name"]);
        $this->assertNull($noneExistingEvent);
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\EventRegister::getConfigFiles
     * @covers \Micronative\ServiceSchema\Config\EventRegister::setConfigFiles
     * @covers \Micronative\ServiceSchema\Config\EventRegister::getEventConfigs
     * @covers \Micronative\ServiceSchema\Config\EventRegister::setEventConfigs
     */
    public function testGetterAndSetters()
    {
        $configs = [];
        $this->eventRegister->setConfigFiles($configs);
        $this->assertSame($configs, $this->eventRegister->getConfigFiles());

        $events = [];
        $this->eventRegister->setEventConfigs($events);
        $this->assertSame($events, $this->eventRegister->getEventConfigs());
    }
}
