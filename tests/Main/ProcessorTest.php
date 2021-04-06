<?php

namespace Micronative\ServiceSchema\Tests\Main;

use Micronative\ServiceSchema\Config\EventRegister;
use Micronative\ServiceSchema\Config\ServiceRegister;
use Micronative\ServiceSchema\Json\JsonReader;
use Micronative\ServiceSchema\Main\Processor;
use Micronative\ServiceSchema\Service\Exception\ServiceException;
use Micronative\ServiceSchema\Service\ServiceFactory;
use Micronative\ServiceSchema\Service\ServiceValidator;
use Micronative\ServiceSchema\Tests\Event\SampleEvent;
use Micronative\ServiceSchema\Main\Exception\ProcessorException;
use PHPUnit\Framework\TestCase;

class ProcessorTest extends TestCase
{
    /** @coversDefaultClass \Micronative\ServiceSchema\Main\Processor */
    protected $processor;

    /** @var string */
    protected $testDir;

    /**
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exception\ConfigException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->processor = new Processor(
            [$this->testDir . "/assets/configs/events.json",$this->testDir . "/assets/configs/events.yml"],
            [$this->testDir . "/assets/configs/services.json"],
            $this->testDir
        );
    }

    /**
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     */
    public function testProcess()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.json"), true);
        $event = new SampleEvent($data);
        $result = $this->processor->process($event);
        $this->assertTrue(is_bool($result));
    }

    /**
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     */
    public function testProcessFailed()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.Failed.json"), true);
        $event = new SampleEvent($data);
        $this->expectException(ServiceException::class);
        $this->processor->process($event);
    }

    /**
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     */
    public function testProcessWithFilteredEvent()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.json"), true);
        $event = new SampleEvent($data);
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessageMatches('%'.ProcessorException::FILTERED_EVENT_ONLY.'%');
        $this->processor->process($event, ['EventOne', 'EventTwo']);
    }

    /**
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     */
    public function testProcessWithNoneRegisteredEvent()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/None.Registered.Event.json"), true);
        $event = new SampleEvent($data);
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage(ProcessorException::NO_REGISTER_EVENTS . $event->getName());
        $this->processor->process($event);
    }

    /**
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     */
    public function testRollback()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.json"), true);
        $event = new SampleEvent($data);
        $result = $this->processor->rollback($event);
        $this->assertTrue(is_bool($result));
    }

    public function testSettersAndGetters()
    {
        $eventRegister = new EventRegister();
        $this->processor->setEventRegister($eventRegister);
        $this->assertSame($eventRegister, $this->processor->getEventRegister());

        $serviceRegister = new ServiceRegister();
        $this->processor->setServiceRegister($serviceRegister);
        $this->assertSame($serviceRegister, $this->processor->getServiceRegister());

        $serviceFactory = new ServiceFactory();
        $this->processor->setServiceFactory($serviceFactory);
        $this->assertSame($serviceFactory, $this->processor->getServiceFactory());

        $serviceValidator = new ServiceValidator();
        $this->processor->setServiceValidator($serviceValidator);
        $this->assertSame($serviceValidator, $this->processor->getServiceValidator());

        $container = new SampleContainer();
        $this->processor->setContainer($container);
        $this->assertEquals($container, $this->processor->getContainer());
    }
}
