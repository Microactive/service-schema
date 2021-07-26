<?php

namespace Tests;

use Micronative\ServiceSchema\Config\EventConfigRegister;
use Micronative\ServiceSchema\Config\ServiceConfigRegister;
use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Exceptions\ProcessorException;
use Micronative\ServiceSchema\Json\JsonReader;
use Micronative\ServiceSchema\Processor;
use Micronative\ServiceSchema\Service\ServiceFactory;
use Micronative\ServiceSchema\Validators\EventValidator;
use Micronative\ServiceSchema\Validators\Exceptions\ValidatorException;
use Micronative\ServiceSchema\Validators\ServiceValidator;
use PHPUnit\Framework\TestCase;
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
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
     */
    public function testProcess()
    {
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.json")
        );
        $event = new SampleEvent($data->name, $data->id, (array)$data->payload);
        $result = $this->processor->process($event);
        $this->assertTrue(is_bool($result));
    }

    /**
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
     */
    public function testProcessWithReturn()
    {
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.json")
        );
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
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.Failed.json")
        );
        $event = new SampleEvent($data->name, $data->id ?? null, (array)$data->payload);
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessageMatches('%' . ValidatorException::INVALIDATED_EVENT . '%');
        $this->processor->process($event);
    }

    /**
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
     */
    public function testProcessWithFilteredEvent()
    {
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.json")
        );
        $event = new SampleEvent($data->name, $data->id, (array)$data->payload);
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessageMatches('%' . ProcessorException::FILTERED_EVENT_ONLY . '%');
        $this->processor->process($event, ['EventOne', 'EventTwo']);
    }

    /**
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
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
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
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
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
     */
    public function testProcessWithNoneExistingServiceEvent()
    {
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/None.Existing.Service.Event.json")
        );
        $event = new SampleEvent($data->name, $data->id ?? null, (array)$data->payload);
        $this->assertTrue($this->processor->process($event));
    }

    /**
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
     */
    public function testProcessWithInvalidServiceClass()
    {
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/Invalid.Service.Class.Event.json")
        );
        $event = new SampleEvent($data->name, $data->id ?? null, (array)$data->payload);
        $this->assertTrue($this->processor->process($event));
    }

    /**
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
     */
    public function testRollback()
    {
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.json")
        );
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
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.Failed.json")
        );
        $event = new SampleEvent($data->name, $data->id ?? null, (array)$data->payload);
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessageMatches('%' . ValidatorException::INVALIDATED_EVENT . '%');
        $this->processor->rollback($event);
    }

    /**
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
     */
    public function testRollbackWithInvalidServiceClass()
    {
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/Invalid.Service.Class.Event.json")
        );
        $event = new SampleEvent($data->name, $data->id ?? null, (array)$data->payload);
        $this->assertTrue($this->processor->rollback($event));
    }

    /**
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
     */
    public function testRollbackWithNoneExistingServiceEvent()
    {
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/None.Existing.Service.Event.json")
        );
        $event = new SampleEvent($data->name, $data->id ?? null, (array)$data->payload);
        $this->assertTrue($this->processor->rollback($event));
    }

    /**
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
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
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
     */
    public function testRollbackWithNoneRegisteredEvent()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/None.Registered.Event.json"));
        $event = new SampleEvent($data->name, $data->id, (array)$data->payload);
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage(ProcessorException::NO_REGISTER_EVENTS . $event->getName());
        $this->processor->rollback($event);
    }

    /**
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
     */
    public function testValidate()
    {
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.json")
        );
        $event = new SampleEvent($data->name, $data->id, (array)$data->payload);
        $result = $this->processor->validate($event, "/assets/schemas/events/Task.json");
        $this->assertTrue(is_bool($result));
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

        $eventValidator = new EventValidator();
        $this->processor->setEventValidator($eventValidator);
        $this->assertSame($eventValidator, $this->processor->getEventValidator());

        $schemaDir = "/app";
        $this->processor->setSchemaDir($schemaDir);
        $this->assertEquals($schemaDir, $this->processor->getSchemaDir())
        ;
        $container = new SampleContainer();
        $this->processor->setContainer($container);
        $this->assertEquals($container, $this->processor->getContainer());
    }
}
