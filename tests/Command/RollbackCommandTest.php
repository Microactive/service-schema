<?php

namespace Micronative\ServiceSchema\Tests\Command;

use Micronative\ServiceSchema\Command\RollbackCommand;
use Micronative\ServiceSchema\Service\ServiceValidator;
use Micronative\ServiceSchema\Tests\Service\Samples\CreateContact;
use Micronative\ServiceSchema\Tests\Service\Samples\CreateTask;
use Micronative\ServiceSchema\Tests\Service\Samples\SampleEvent;
use PHPUnit\Framework\TestCase;

class RollbackCommandTest extends TestCase
{
    /** @coversDefaultClass \Micronative\ServiceSchema\Command\RollbackCommand */
    private $command;

    public function setUp(): void
    {
        parent::setUp();
        $testDir = dirname(dirname(__FILE__));
        $validator = new ServiceValidator($testDir);
        $event = new SampleEvent(['id' => 1, 'event' => 'Test.Event.Name', 'payload' => ['name' => 'Ken']]);
        $service = new CreateContact();
        $this->command = new RollbackCommand($validator, $service, $event);
    }

    /**
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     */
    public function testExecute()
    {
        $result = $this->command->execute();
        $this->assertEquals('Contact creation has been rollback.', $result);
    }
}
