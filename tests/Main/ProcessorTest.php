<?php

namespace Micronative\ServiceSchema\Tests\Main;

use Micronative\ServiceSchema\Json\JsonReader;
use Micronative\ServiceSchema\Main\Processor;
use Micronative\ServiceSchema\Service\Exception\ServiceException;
use Micronative\ServiceSchema\Tests\Event\SampleEvent;
use PHPUnit\Framework\TestCase;

class ProcessorTest extends TestCase
{
    protected $testDir;

    /** @var \Micronative\ServiceSchema\Main\Processor */
    protected $processor;

    /**
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Config\Exception\ConfigException
     */
    public function setUp()
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->processor = new Processor([$this->testDir . "/assets/configs/events.json"], [$this->testDir . "/assets/configs/services.json"], $this->testDir);
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
    public function testRollback()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.json"), true);
        $event = new SampleEvent($data);
        $result = $this->processor->rollback($event);
        $this->assertTrue(is_bool($result));
    }

    public function testSettersAndGetters()
    {
        $eventRegister = $this->processor->getEventRegister();
        $this->processor->setEventRegister($eventRegister);
        $this->assertSame($eventRegister, $this->processor->getEventRegister());

        $serviceRegister = $this->processor->getServiceRegister();
        $this->processor->setServiceRegister($serviceRegister);
        $this->assertSame($serviceRegister, $this->processor->getServiceRegister());

        $serviceFactory = $this->processor->getServiceFactory();
        $this->processor->setServiceFactory($serviceFactory);
        $this->assertSame($serviceFactory, $this->processor->getServiceFactory());

        $serviceValidator = $this->processor->getServiceValidator();
        $this->processor->setServiceValidator($serviceValidator);
        $this->assertSame($serviceValidator, $this->processor->getServiceValidator());
    }
}
