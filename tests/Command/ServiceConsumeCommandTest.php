<?php

namespace Tests\Command;

use Micronative\ServiceSchema\Command\ServiceConsumeCommand;
use Micronative\ServiceSchema\Validators\Exceptions\ValidatorException;
use Micronative\ServiceSchema\Validators\ServiceValidator;
use PHPUnit\Framework\TestCase;
use Tests\Service\Samples\CreateTask;
use Tests\Service\Samples\SampleEvent;

class ServiceConsumeCommandTest extends TestCase
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
        $this->service->setSchema("/assets/schemas/services/CreateTask.json");
        $this->command = new ServiceConsumeCommand($this->validator, $this->service, $this->event);
        $result = $this->command->execute();
        $this->assertEquals('Task created.', $result);
    }

    /**
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
     */
    public function testExecuteThrowsException()
    {
        $this->service->setSchema("/assets/schemas/services/CreateContact.json");
        $this->command = new ServiceConsumeCommand($this->validator, $this->service, $this->event);
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessageMatches('%' . ValidatorException::INVALIDATED_EVENT . '%');
        $this->command->execute();
    }
}
