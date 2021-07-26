<?php

namespace Tests;

use Micronative\ServiceSchema\Config\EventConfigRegister;
use Micronative\ServiceSchema\Config\ServiceConfigRegister;
use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Json\JsonReader;
use Micronative\ServiceSchema\Processor;
use Micronative\ServiceSchema\Service\Exceptions\ServiceException;
use Micronative\ServiceSchema\Service\ServiceFactory;
use Micronative\ServiceSchema\Service\ServiceValidator;
use Micronative\ServiceSchema\Exceptions\ProcessorException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Tests\Event\SampleEvent;

class ProcessorTest extends TestCase
{
    /** @coversDefaultClass \Micronative\ServiceSchema\Processor */
    protected $processor;

    /** @var string */
    protected $testDir;

    /**
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exceptions\ConfigException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(__FILE__);
        $this->processor = new Processor(
            [$this->testDir . "/assets/configs/events.json", $this->testDir . "/assets/configs/events.yml"],
            [$this->testDir . "/assets/configs/services.json", $this->testDir . "/assets/configs/services.yml"],
            $this->testDir
        );
    }

    /**
     * @covers \Micronative\ServiceSchema\Processor::process
     * @covers \Micronative\ServiceSchema\Processor::runService
     * @covers \Micronative\ServiceSchema\Processor::runCallbacks
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     */
    public function testProcess()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.json"));
        $event = new SampleEvent($data->name, $data->id, (array)$data->payload);
        $result = $this->processor->process($event);
        $this->assertTrue(is_bool($result));
    }

    /**
     * @covers \Micronative\ServiceSchema\Processor::process
     * @covers \Micronative\ServiceSchema\Processor::runService
     * @covers \Micronative\ServiceSchema\Processor::runCallbacks
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     */
    public function testProcessWithReturn()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.json"));
        $event = new SampleEvent($data->name, $data->id, (array)$data->payload);
        $result = $this->processor->process($event, null, true);
        $this->assertInstanceOf(AbstractEvent::class, $result);
    }

    /**
     * @covers \Micronative\ServiceSchema\Processor::process
     * @covers \Micronative\ServiceSchema\Processor::runService
     * @covers \Micronative\ServiceSchema\Processor::runCallbacks
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     */
    public function testProcessFailed()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.Failed.json"));
        $event = new SampleEvent($data->name, $data->id ?? null, (array)$data->payload);
        $this->expectException(ServiceException::class);
        $this->processor->process($event);
    }

    /**
     * @covers \Micronative\ServiceSchema\Processor::process
     * @covers \Micronative\ServiceSchema\Processor::runService
     * @covers \Micronative\ServiceSchema\Processor::runCallbacks
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     */
    public function testProcessWithFilteredEvent()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.json"));
        $event = new SampleEvent($data->name, $data->id, (array)$data->payload);
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessageMatches('%'.ProcessorException::FILTERED_EVENT_ONLY.'%');
        $this->processor->process($event, ['EventOne', 'EventTwo']);
    }

    /**
     * @covers \Micronative\ServiceSchema\Processor::process
     * @covers \Micronative\ServiceSchema\Processor::runService
     * @covers \Micronative\ServiceSchema\Processor::runCallbacks
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     */
    public function testProcessWithNoneRegisteredEvent()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/None.Registered.Event.json"));
        $event = new SampleEvent($data->name, $data->id ?? null, (array)$data->payload);
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage(ProcessorException::NO_REGISTER_EVENTS . $event->getName());
        $this->processor->process($event);
    }

    /**
     * @covers \Micronative\ServiceSchema\Processor::process
     * @covers \Micronative\ServiceSchema\Processor::runService
     * @covers \Micronative\ServiceSchema\Processor::runCallbacks
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     */
    public function testProcessWithEmptyServiceEvent()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Empty.Service.Event.json"));
        $event = new SampleEvent($data->name, $data->id ?? null, (array)$data->payload);
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage(ProcessorException::NO_REGISTER_SERVICES . $event->getName());
        $this->processor->process($event);
    }

    /**
     * @covers \Micronative\ServiceSchema\Processor::process
     * @covers \Micronative\ServiceSchema\Processor::runService
     * @covers \Micronative\ServiceSchema\Processor::runCallbacks
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     */
    public function testProcessWithNoneExistingServiceEvent()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/None.Existing.Service.Event.json"));
        $event = new SampleEvent($data->name, $data->id ?? null, (array)$data->payload);
        $this->assertTrue($this->processor->process($event));
    }

    /**
     * @covers \Micronative\ServiceSchema\Processor::process
     * @covers \Micronative\ServiceSchema\Processor::runService
     * @covers \Micronative\ServiceSchema\Processor::runCallbacks
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     */
    public function testProcessWithInvalidServiceClass()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Invalid.Service.Class.Event.json"));
        $event = new SampleEvent($data->name, $data->id ?? null, (array)$data->payload);
        $this->assertTrue($this->processor->process($event));
    }

    /**
     * @covers \Micronative\ServiceSchema\Processor::rollback
     * @covers \Micronative\ServiceSchema\Processor::rollbackService
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     */
    public function testRollback()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.json"));
        $event = new SampleEvent($data->name, $data->id ?? null, (array)$data->payload);
        $result = $this->processor->rollback($event);
        $this->assertTrue($result);
    }

    /**
     * @covers \Micronative\ServiceSchema\Processor::rollback
     * @covers \Micronative\ServiceSchema\Processor::rollbackService
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     */
    public function testRollbackWithInvalidValidation()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.Failed.json"));
        $event = new SampleEvent($data->name, $data->id ?? null, (array)$data->payload);
        $this->expectException(ServiceException::class);
        $this->processor->rollback($event);
    }

    /**
     * @covers \Micronative\ServiceSchema\Processor::rollback
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     */
    public function testRollbackWithInvalidServiceClass()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Invalid.Service.Class.Event.json"));
        $event = new SampleEvent($data->name, $data->id ?? null, (array)$data->payload);
        $this->assertTrue($this->processor->rollback($event));
    }

    /**
     * @covers \Micronative\ServiceSchema\Processor::rollback
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     */
    public function testRollbackWithNoneExistingServiceEvent()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/None.Existing.Service.Event.json"));
        $event = new SampleEvent($data->name, $data->id ?? null, (array)$data->payload);
        $this->assertTrue($this->processor->rollback($event));
    }

    /**
     * @covers \Micronative\ServiceSchema\Processor::rollback
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     */
    public function testRollbackWithEmptyServiceEvent()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Empty.Service.Event.json"));
        $event = new SampleEvent($data->name, $data->id ?? null, (array)$data->payload);
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage(ProcessorException::NO_REGISTER_SERVICES . $event->getName());
        $this->processor->rollback($event);
    }

    /**
     * @covers \Micronative\ServiceSchema\Processor::rollback
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     */
    public function testRollbackWithNoneRegisteredEvent()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/None.Registered.Event.json"));
        $event = new SampleEvent($data->name, $data->id, (array)$data->payload);
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage(ProcessorException::NO_REGISTER_EVENTS . $event->getName());
        $this->processor->rollback($event);
    }

    public function testSettersAndGetters()
    {
        $eventRegister = new EventConfigRegister();
        $this->processor->setEventConfigRegister($eventRegister);
        $this->assertSame($eventRegister, $this->processor->getEventConfigRegister());

        $serviceRegister = new ServiceConfigRegister();
        $this->processor->setServiceConfigRegister($serviceRegister);
        $this->assertSame($serviceRegister, $this->processor->getServiceConfigRegister());

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
