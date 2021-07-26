<?php

namespace Tests\Event;

use JsonSchema\Validator;
use Micronative\ServiceSchema\Event\EventValidator;
use Micronative\ServiceSchema\Event\Exception\EventValidatorException;
use PHPUnit\Framework\TestCase;

class EventValidatorTest extends TestCase
{
    /** @coversDefaultClass \Micronative\ServiceSchema\Event\EventValidator */
    protected $validator;

    /** @var string */
    protected $testDir;

    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->validator = new EventValidator(new Validator());
    }

    /**
     * @covers \Micronative\ServiceSchema\Event\EventValidator::__construct
     * @covers \Micronative\ServiceSchema\Event\EventValidator::validate
     * @throws \Micronative\ServiceSchema\Event\Exception\EventValidatorException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     */
    public function testValidateWithEmptyEvent()
    {
        $event = new SampleInvalidEvent('SomeName');
        $this->expectException(EventValidatorException::class);
        $this->expectExceptionMessage(EventValidatorException::INVALID_JSON_STRING);
        $this->validator->validate($event);
    }

    /**
     * @covers \Micronative\ServiceSchema\Event\EventValidator::validate
     */
    public function testValidateWithEmptySchema()
    {
        $event = new SampleEvent("SomeName");
        $this->expectException(EventValidatorException::class);
        $this->expectExceptionMessage(EventValidatorException::MISSING_EVENT_SCHEMA);
        $this->validator->validate($event);
    }

    /**
     * @covers \Micronative\ServiceSchema\Event\EventValidator::validate
     */
    public function testValidateFailed()
    {
        $event = new SampleEvent("SomeName");
        $this->expectException(EventValidatorException::class);
        $this->expectExceptionMessageMatches("%" . EventValidatorException::INVALIDATED_EVENT_MESSAGE . "%");
        $this->validator->validate($event, $this->testDir . '/assets/schemas/UpdateContact.json');
    }

    /**
     * @covers \Micronative\ServiceSchema\Event\EventValidator::validate
     */
    public function testValidateSuccessful()
    {
        $event = new SampleEvent("SomeName");
        $event->setName('User.Created')->setPayload(["name" => "Ken"]);
        $validated = $this->validator->validate($event, $this->testDir . '/assets/schemas/SampleEvent.json', true);
        $this->assertTrue($validated);
    }
}
