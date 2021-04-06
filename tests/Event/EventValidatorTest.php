<?php

namespace Micronative\ServiceSchema\Tests\Event;

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
    }

    /**
     * @covers \Micronative\ServiceSchema\Event\EventValidator::validate
     * @throws \Micronative\ServiceSchema\Event\Exception\EventValidatorException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function testValidateWithEmptyEvent(){
        $event = new SampleInvalidEvent();
        $this->expectException(EventValidatorException::class);
        $this->expectExceptionMessage(EventValidatorException::INVALID_JSON_STRING);
        EventValidator::validate($event);
    }

    /**
     * @covers \Micronative\ServiceSchema\Event\EventValidator::validate
     * @throws \Micronative\ServiceSchema\Event\Exception\EventValidatorException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function testValidateWithEmptySchema(){
        $event = new SampleEvent();
        $this->expectException(EventValidatorException::class);
        $this->expectExceptionMessage(EventValidatorException::MISSING_EVENT_SCHEMA);
        EventValidator::validate($event);
    }

    /**
     * @covers \Micronative\ServiceSchema\Event\EventValidator::validate
     * @throws \Micronative\ServiceSchema\Event\Exception\EventValidatorException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function testValidateFailed(){
        $event = new SampleEvent();
        $this->expectException(EventValidatorException::class);
        $this->expectExceptionMessageMatches("%".EventValidatorException::INVALIDATED_EVENT_MESSAGE."%");
        EventValidator::validate($event, $this->testDir.'/assets/schemas/UpdateContact.json');
    }

    /**
     * @covers \Micronative\ServiceSchema\Event\EventValidator::validate
     * @throws \Micronative\ServiceSchema\Event\Exception\EventValidatorException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function testValidateSuccessful(){
        $event = new SampleEvent();
        $event->setName('User.Created')->setPayload((object) ["name" => "Ken"]);
        $validated = EventValidator::validate($event, $this->testDir.'/assets/schemas/SampleEvent.json');
        $this->assertTrue($validated);
    }
}
