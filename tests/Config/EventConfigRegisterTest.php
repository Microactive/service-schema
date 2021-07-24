<?php

namespace Micronative\ServiceSchema\Tests\Config;

use Micronative\ServiceSchema\Config\EventConfig;
use Micronative\ServiceSchema\Config\EventConfigRegister;
use Micronative\ServiceSchema\Config\Exceptions\ConfigException;
use PHPUnit\Framework\TestCase;

class EventConfigRegisterTest extends TestCase
{
    /** @coversDefaultClass \Micronative\ServiceSchema\Config\EventConfigRegister */
    protected $eventConfigRegister;

    /** @var string */
    protected $testDir;

    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->eventConfigRegister = new EventConfigRegister([$this->testDir . "/assets/configs/events.json"]);
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\EventConfigRegister::loadEventConfigs
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     */
    public function testLoadEventsWithEmptyConfigs()
    {
        $this->eventConfigRegister->setConfigFiles(null);
        $this->eventConfigRegister->loadEventConfigs();
        $this->assertEquals([], $this->eventConfigRegister->getEventConfigs());
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\EventConfigRegister::loadEventConfigs
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     */
    public function testLoadEventsWithUnsupportedFiles()
    {
        $this->eventConfigRegister->setConfigFiles([$this->testDir . "/assets/configs/events.csv"]);
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage(ConfigException::UNSUPPORTED_FILE_FORMAT . 'csv');
        $this->eventConfigRegister->loadEventConfigs();
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\EventConfigRegister::loadEventConfigs
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     */
    public function testLoadEventConfigs()
    {
        $this->eventConfigRegister->loadEventConfigs();
        $eventConfigs = $this->eventConfigRegister->getEventConfigs();

        $this->assertTrue(is_array($eventConfigs));
        $this->assertTrue(isset($eventConfigs["Users.afterSaveCommit.Create"]));
        $this->assertTrue(isset($eventConfigs["Users.afterSaveCommit.Update"]));
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\EventConfigRegister::registerEventConfig
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     */
    public function testRegisterEvent()
    {
        $this->eventConfigRegister->loadEventConfigs();
        $config1 = new EventConfig("Event.Name", ["FirstServiceClass"]);
        $config2 = new EventConfig("Event.Name", ["SecondServiceClass"]);
        $this->eventConfigRegister->registerEventConfig($config1);
        $this->eventConfigRegister->registerEventConfig($config2);
        $eventConfigs = $this->eventConfigRegister->getEventConfigs();
        $config = $eventConfigs["Event.Name"];

        $this->assertIsArray($eventConfigs);
        $this->assertArrayHasKey("Event.Name", $eventConfigs);
        $this->assertInstanceOf(EventConfig::class, $config);
        $this->assertEquals(["FirstServiceClass", "SecondServiceClass"], $config->getServiceClasses());
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\EventConfigRegister::retrieveEventConfig
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     */
    public function testRetrieveEvent()
    {
        $this->eventConfigRegister->loadEventConfigs();
        $config = new EventConfig("Event.Name", ["SomeServiceClass"]);
        $this->eventConfigRegister->registerEventConfig($config);
        $eventConfig = $this->eventConfigRegister->retrieveEventConfig("Event.Name");
        $noneExistingEvent = $this->eventConfigRegister->retrieveEventConfig("Not.Existing.Name");

        $this->assertInstanceOf(EventConfig::class, $eventConfig);
        $this->assertNull($noneExistingEvent);
        $this->assertEquals("Event.Name", $eventConfig->getName());
        $this->assertEquals(["SomeServiceClass"], $eventConfig->getServiceClasses());
    }

    /**
     * @covers \Micronative\ServiceSchema\Config\EventConfigRegister::getConfigFiles
     * @covers \Micronative\ServiceSchema\Config\EventConfigRegister::setConfigFiles
     * @covers \Micronative\ServiceSchema\Config\EventConfigRegister::getEventConfigs
     * @covers \Micronative\ServiceSchema\Config\EventConfigRegister::setEventConfigs
     */
    public function testGetterAndSetters()
    {
        $configs = [];
        $this->eventConfigRegister->setConfigFiles($configs);
        $this->assertSame($configs, $this->eventConfigRegister->getConfigFiles());

        $events = [];
        $this->eventConfigRegister->setEventConfigs($events);
        $this->assertSame($events, $this->eventConfigRegister->getEventConfigs());
    }
}
