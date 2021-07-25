<?php

namespace Tests\Command;

use Micronative\ServiceSchema\Command\ConsumeCommand;
use Micronative\ServiceSchema\Service\Exceptions\ServiceException;
use Micronative\ServiceSchema\Service\ServiceValidator;
use PHPUnit\Framework\TestCase;
use Tests\Service\Samples\CreateTask;
use Tests\Service\Samples\SampleEvent;

class ConsumeCommandTest extends TestCase
{
    /** @coversDefaultClass \Micronative\ServiceSchema\Command\ConsumeCommand */
    private $command;

    /** @var \Micronative\ServiceSchema\Service\ServiceValidator */
    private $validator;

    /** @var \Micronative\ServiceSchema\Service\ServiceInterface */
    private $service;

    /** @var \Micronative\ServiceSchema\Event\AbstractEvent */
    private $event;

    public function setUp(): void
    {
        parent::setUp();
        $testDir = dirname(dirname(__FILE__));
        $this->validator = new ServiceValidator($testDir);
        $this->event = new SampleEvent('Test.Event.Name', 1, ['name' => 'Ken']);
        $this->service = new CreateTask();
    }

    /**
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     */
    public function testExecute()
    {
        $this->service->setJsonSchema("/assets/schemas/CreateTask.json");
        $this->command = new ConsumeCommand($this->validator, $this->service, $this->event);
        $result = $this->command->execute();
        $this->assertEquals('Task created.', $result);
    }

    /**
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     */
    public function testExecuteThrowsException()
    {
        $this->service->setJsonSchema("/assets/schemas/CreateContact.json");
        $this->command = new ConsumeCommand($this->validator, $this->service, $this->event);
        $this->expectException(ServiceException::class);
        $this->command->execute();
    }
}
