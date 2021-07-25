<?php

namespace Tests\Command;

use Micronative\ServiceSchema\Command\RollbackCommand;
use Micronative\ServiceSchema\Service\ServiceValidator;
use PHPUnit\Framework\TestCase;
use Tests\Service\Samples\CreateContact;
use Tests\Service\Samples\SampleEvent;

class RollbackCommandTest extends TestCase
{
    /** @coversDefaultClass \Micronative\ServiceSchema\Command\RollbackCommand */
    private $command;

    public function setUp(): void
    {
        parent::setUp();
        $testDir = dirname(dirname(__FILE__));
        $validator = new ServiceValidator($testDir);
        $event = new SampleEvent('Test.Event.Name', 1, ['name' => 'Ken']);
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
