<?php

namespace Micronative\ServiceSchema\Tests\Service;

use PHPUnit\Framework\TestCase;
use Micronative\ServiceSchema\Json\JsonReader;
use Micronative\ServiceSchema\Service\ServiceValidator;
use Micronative\ServiceSchema\Tests\Service\Samples\CreateContact;
use Micronative\ServiceSchema\Service\Exceptions\ServiceException;

class ServiceValidatorTest extends TestCase
{
    /** @var string */
    protected $testDir;

    /** @var ServiceValidator */
    protected $serviceValidator;

    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->serviceValidator = new ServiceValidator($this->testDir);
    }

    /**
     * @covers \Micronative\ServiceSchema\Service\ServiceValidator::validate
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     */
    public function testValidateWithEmptyJson()
    {
        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(ServiceException::MISSING_JSON_STRING);
        $this->serviceValidator->validate();
    }

    /**
     * @covers \Micronative\ServiceSchema\Service\ServiceValidator::validate
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     */
    public function testValidateWithEmptyJsonSchema()
    {
        $file = $this->testDir . "/assets/events/Users.afterSaveCommit.Create.json";
        $jsonObject = JsonReader::decode(JsonReader::read($file));
        $service = new CreateContact();
        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(ServiceException::MISSING_SERVICE_SCHEMA);
        $this->serviceValidator->validate($jsonObject, $service);
    }

    /**
     * @covers \Micronative\ServiceSchema\Service\ServiceValidator::validate
     * @covers \Micronative\ServiceSchema\Service\ServiceValidator::getValidator
     * @covers \Micronative\ServiceSchema\Service\ServiceValidator::setValidator
     * @covers \Micronative\ServiceSchema\Service\ServiceValidator::getSchemaDir
     * @covers \Micronative\ServiceSchema\Service\ServiceValidator::setSchemaDir
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     */
    public function testValidate()
    {
        $file = $this->testDir . "/assets/events/Users.afterSaveCommit.Create.json";
        $jsonObject = JsonReader::decode(JsonReader::read($file));
        $service = new CreateContact();
        $service->setJsonSchema("/assets/schemas/CreateContact.json");
        $validator = $this->serviceValidator->validate($jsonObject, $service);
        $this->assertTrue($validator->isValid());

        $validator = $this->serviceValidator->getValidator();
        $this->serviceValidator->setValidator($validator);
        $this->assertSame($validator, $this->serviceValidator->getValidator());

        $this->serviceValidator->setSchemaDir($this->testDir);
        $schemaDir = $this->serviceValidator->getSchemaDir();
        $this->assertSame($schemaDir, $this->serviceValidator->getSchemaDir());
    }
}
