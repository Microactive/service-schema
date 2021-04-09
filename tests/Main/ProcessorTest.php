<?php

namespace Micronative\ServiceSchema\Tests\Main;

use Micronative\ServiceSchema\Config\EventRegister;
use Micronative\ServiceSchema\Config\ServiceRegister;
use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Json\JsonReader;
use Micronative\ServiceSchema\Main\Processor;
use Micronative\ServiceSchema\Service\Exception\ServiceException;
use Micronative\ServiceSchema\Service\ServiceFactory;
use Micronative\ServiceSchema\Service\ServiceValidator;
use Micronative\ServiceSchema\Tests\Event\SampleEvent;
use Micronative\ServiceSchema\Main\Exception\ProcessorException;
use PHPUnit\Framework\TestCase;

use function Webmozart\Assert\Tests\StaticAnalysis\null;

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
            [$this->testDir . "/assets/configs/events.json", $this->testDir . "/assets/configs/events.yml"],
            [$this->testDir . "/assets/configs/services.json", $this->testDir . "/assets/configs/services.yml"],
            $this->testDir
        );
    }

    /**
     * @covers \Micronative\ServiceSchema\Main\Processor::process
     * @covers \Micronative\ServiceSchema\Main\Processor::runService
     * @covers \Micronative\ServiceSchema\Main\Processor::runCallbacks
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
     * @covers \Micronative\ServiceSchema\Main\Processor::process
     * @covers \Micronative\ServiceSchema\Main\Processor::runService
     * @covers \Micronative\ServiceSchema\Main\Processor::runCallbacks
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     */
    public function testProcessWithReturn()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.json"), true);
        $event = new SampleEvent($data);
        $result = $this->processor->process($event, null, true);
        $this->assertInstanceOf(AbstractEvent::class, $result);
    }

    /**
     * @covers \Micronative\ServiceSchema\Main\Processor::process
     * @covers \Micronative\ServiceSchema\Main\Processor::runService
     * @covers \Micronative\ServiceSchema\Main\Processor::runCallbacks
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
     * @covers \Micronative\ServiceSchema\Main\Processor::process
     * @covers \Micronative\ServiceSchema\Main\Processor::runService
     * @covers \Micronative\ServiceSchema\Main\Processor::runCallbacks
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
     * @covers \Micronative\ServiceSchema\Main\Processor::process
     * @covers \Micronative\ServiceSchema\Main\Processor::runService
     * @covers \Micronative\ServiceSchema\Main\Processor::runCallbacks
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
     * @covers \Micronative\ServiceSchema\Main\Processor::process
     * @covers \Micronative\ServiceSchema\Main\Processor::runService
     * @covers \Micronative\ServiceSchema\Main\Processor::runCallbacks
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     */
    public function testProcessWithEmptyServiceEvent()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Empty.Service.Event.json"), true);
        $event = new SampleEvent($data);
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage(ProcessorException::NO_REGISTER_SERVICES . $event->getName());
        $this->processor->process($event);
    }

    /**
     * @covers \Micronative\ServiceSchema\Main\Processor::process
     * @covers \Micronative\ServiceSchema\Main\Processor::runService
     * @covers \Micronative\ServiceSchema\Main\Processor::runCallbacks
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     */
    public function testProcessWithNoneExistingServiceEvent()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/None.Existing.Service.Event.json"), true);
        $event = new SampleEvent($data);
        $this->assertTrue($this->processor->process($event));
    }

    /**
     * @covers \Micronative\ServiceSchema\Main\Processor::process
     * @covers \Micronative\ServiceSchema\Main\Processor::runService
     * @covers \Micronative\ServiceSchema\Main\Processor::runCallbacks
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     */
    public function testProcessWithInvalidServiceClass()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Invalid.Service.Class.Event.json"), true);
        $event = new SampleEvent($data);
        $this->assertTrue($this->processor->process($event));
    }

    /**
     * @covers \Micronative\ServiceSchema\Main\Processor::rollback
     * @covers \Micronative\ServiceSchema\Main\Processor::rollbackService
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     */
    public function testRollback()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.json"), true);
        $event = new SampleEvent($data);
        $result = $this->processor->rollback($event);
        $this->assertTrue($result);
    }

    /**
     * @covers \Micronative\ServiceSchema\Main\Processor::rollback
     * @covers \Micronative\ServiceSchema\Main\Processor::rollbackService
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     */
    public function testRollbackWithInvalidValidation()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.Failed.json"), true);
        $event = new SampleEvent($data);
        $this->expectException(ServiceException::class);
        $this->processor->rollback($event);
    }

    /**
     * @covers \Micronative\ServiceSchema\Main\Processor::rollback
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     */
    public function testRollbackWithInvalidServiceClass()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Invalid.Service.Class.Event.json"), true);
        $event = new SampleEvent($data);
        $this->assertTrue($this->processor->rollback($event));
    }

    /**
     * @covers \Micronative\ServiceSchema\Main\Processor::rollback
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     */
    public function testRollbackWithNoneExistingServiceEvent()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/None.Existing.Service.Event.json"), true);
        $event = new SampleEvent($data);
        $this->assertTrue($this->processor->rollback($event));
    }

    /**
     * @covers \Micronative\ServiceSchema\Main\Processor::rollback
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     */
    public function testRollbackWithEmptyServiceEvent()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Empty.Service.Event.json"), true);
        $event = new SampleEvent($data);
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage(ProcessorException::NO_REGISTER_SERVICES . $event->getName());
        $this->processor->rollback($event);
    }

    /**
     * @covers \Micronative\ServiceSchema\Main\Processor::rollback
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Main\Exception\ProcessorException
     */
    public function testRollbackWithNoneRegisteredEvent()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/None.Registered.Event.json"), true);
        $event = new SampleEvent($data);
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage(ProcessorException::NO_REGISTER_EVENTS . $event->getName());
        $this->processor->rollback($event);
    }

    /**
     * @covers \Micronative\ServiceSchema\Main\Processor::runCallbacks
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function testRunCallbacksWithEmpty()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.json"), true);
        $event = new SampleEvent($data);
        $result = $this->processor->runCallbacks($event);
        $this->assertTrue($result);
    }

    /**
     * @covers \Micronative\ServiceSchema\Main\Processor::runCallbacks
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function testRunCallbacksWithInvalidService()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Invalid.Service.Class.Event.json"), true);
        $event = new SampleEvent($data);
        $result = $this->processor->runCallbacks($event, ['Micronative\ServiceSchema\Tests\Service\Samples\InvalidService']);
        $this->assertTrue($result);
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
