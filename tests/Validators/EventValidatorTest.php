<?php

namespace Tests\Validators;

use JsonSchema\Validator;
use Micronative\ServiceSchema\Json\Exceptions\JsonException;
use Micronative\ServiceSchema\Validators\EventValidator;
use Micronative\ServiceSchema\Validators\Exceptions\ValidatorException;
use PHPUnit\Framework\TestCase;
use Tests\Event\SampleInvalidEvent;
use Tests\Event\SampleEvent;

class EventValidatorTest extends TestCase
{
    /** @coversDefaultClass \Micronative\ServiceSchema\Validators\EventValidator */
    protected $validator;

    /** @var string */
    protected $testDir;

    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->validator = new EventValidator($this->testDir, new Validator());
    }

    /**
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
     */
    public function testValidateWithInvalidJsonEvent()
    {
        $event = new SampleInvalidEvent('SomeName');
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage(ValidatorException::INVALID_JSON);
        $this->validator->validateEvent($event);
    }

    /**
     * @covers \Micronative\ServiceSchema\Validators\EventValidator::validateEvent
     */
    public function testValidateWithInvalidSchema()
    {
        $event = new SampleEvent("SomeName");
        $this->expectException(JsonException::class);
        $this->expectExceptionMessage(JsonException::INVALID_JSON_FILE);
        $this->validator->validateEvent($event);
    }

    /**
     * @covers \Micronative\ServiceSchema\Validators\EventValidator::validateEvent
     */
    public function testValidateFailed()
    {
        $event = new SampleEvent("SomeName");
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessageMatches("%" . ValidatorException::INVALIDATED_EVENT . "%");
        $this->validator->validateEvent($event, '/assets/schemas/UpdateContact.json');
    }

    /**
     * @covers \Micronative\ServiceSchema\Validators\EventValidator::validateEvent
     */
    public function testValidateWithNoneExistingSchema()
    {
        $event = new SampleEvent("SomeName");
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessageMatches("%" . ValidatorException::INVALID_SCHEMA . "%");
        $this->validator->validateEvent($event, '/assets/schemas/InvalidJsonSchema.json');
    }

    /**
     * @covers \Micronative\ServiceSchema\Validators\EventValidator::validateEvent
     */
    public function testValidateSuccessful()
    {
        $event = new SampleEvent("SomeName");
        $event->setName('User.Created')->setPayload(["name" => "Ken"]);
        $validated = $this->validator->validateEvent($event,  '/assets/schemas/SampleEvent.json', true);
        $this->assertTrue($validated);
    }
}
