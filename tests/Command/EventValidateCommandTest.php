<?php

namespace Tests\Command;

use Micronative\ServiceSchema\Command\EventValidateCommand;
use Micronative\ServiceSchema\Exceptions\ValidatorException;
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
     * @throws \Micronative\ServiceSchema\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Exceptions\ValidatorException
     */
    public function testExecute()
    {
        $this->service->setSchema("/assets/schemas/services/CreateTask.json");
        $this->event->setSchema("/assets/schemas/services/CreateTask.json");
        $this->command = new EventValidateCommand(
            $this->validator,
            $this->event,
            true
        );
        $result = $this->command->execute();
        $this->assertTrue($result);
    }

    /**
     * @throws \Micronative\ServiceSchema\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Exceptions\ValidatorException
     */
    public function testExecuteThrowsException()
    {
        $this->service->setSchema("/assets/schemas/services/CreateContact.json");
        $this->event->setSchema("/assets/schemas/services/CreateContact.json");
        $this->command = new EventValidateCommand($this->validator, $this->event, true);
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessageMatches('%' . ValidatorException::INVALIDATED_EVENT . '%');
        $this->command->execute();
    }
}
