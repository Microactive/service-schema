<?php

namespace Tests\Command;

use Micronative\ServiceSchema\Command\EventValidateCommand;
use Micronative\ServiceSchema\Command\ServiceConsumeCommand;
use Micronative\ServiceSchema\Validators\Exceptions\ValidatorException;
use Micronative\ServiceSchema\Validators\ServiceValidator;
use PHPUnit\Framework\TestCase;
use Tests\Service\Samples\CreateTask;
use Tests\Service\Samples\SampleEvent;

class EventValidateCommandTest extends TestCase
{
    /** @coversDefaultClass \Micronative\ServiceSchema\Command\EventValidateCommand */
    private $command;

    /** @var \Micronative\ServiceSchema\Validators\ServiceValidator */
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
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
     */
    public function testExecute()
    {
        $this->service->setJsonSchema("/assets/schemas/CreateTask.json");
        $this->command = new EventValidateCommand($this->validator, $this->event, "/assets/schemas/CreateTask.json", true);
        $result = $this->command->execute();
        $this->assertTrue($result);
    }

    /**
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
     */
    public function testExecuteThrowsException()
    {
        $this->service->setJsonSchema("/assets/schemas/CreateContact.json");
        $this->command = new EventValidateCommand($this->validator, $this->event, "/assets/schemas/CreateContact.json", true);
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessageMatches('%' . ValidatorException::INVALIDATED_EVENT . '%');
        $this->command->execute();
    }
}
