<?php

namespace Micronative\ServiceSchema\Tests\Service;

use PHPUnit\Framework\TestCase;
use Micronative\ServiceSchema\Json\JsonReader;
use Micronative\ServiceSchema\Service\ServiceValidator;
use Micronative\ServiceSchema\Tests\Service\Samples\CreateContact;

class ServiceValidatorTest extends TestCase
{
    /** @var string */
    protected $testDir;

    /** @var ServiceValidator */
    protected $serviceValidator;

    public function setUp()
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->serviceValidator = new ServiceValidator();
    }

    /**
     * @covers \Micronative\ServiceSchema\Service\ServiceValidator::validate
     * @covers \Micronative\ServiceSchema\Service\ServiceValidator::getValidator
     * @covers \Micronative\ServiceSchema\Service\ServiceValidator::setValidator
     * @covers \Micronative\ServiceSchema\Service\ServiceValidator::getSchemaDir
     * @covers \Micronative\ServiceSchema\Service\ServiceValidator::setSchemaDir
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     */
    public function testValidate()
    {
        $file = $this->testDir . "/assets/events/Users.afterSaveCommit.Create.json";
        $jsonObject = JsonReader::decode(JsonReader::read($file));
        $service = new CreateContact();
        $service->setJsonSchema($this->testDir . "/assets/schemas/CreateContact.json");
        $validator = $this->serviceValidator->validate($jsonObject, $service);
        var_dump($validator->getErrors());
        $this->assertTrue($validator->isValid());

        $validator = $this->serviceValidator->getValidator();
        $this->serviceValidator->setValidator($validator);
        $this->assertSame($validator, $this->serviceValidator->getValidator());

        $this->serviceValidator->setSchemaDir($this->testDir);
        $schemaDir = $this->serviceValidator->getSchemaDir();
        $this->assertSame($schemaDir, $this->serviceValidator->getSchemaDir());
    }
}
