<?php

namespace Micronative\ServiceSchema\Service;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;
use Micronative\ServiceSchema\Json\JsonReader;
use Micronative\ServiceSchema\Service\Exceptions\ServiceException;

class ServiceValidator
{
    /** @var \JsonSchema\Validator */
    protected $validator;

    /** @var string */
    protected $schemaDir;

    /**
     * EventValidator constructor.
     *
     * @param string|null $schemaDir relative dir in the application
     */
    public function __construct(string $schemaDir = null)
    {
        $this->validator = new Validator();
        $this->schemaDir = $schemaDir;
    }

    /**
     * @param \stdClass|null $jsonObject
     * @param \Micronative\ServiceSchema\Service\ServiceInterface|null $service
     * @param bool $applyDefaultValues
     * @return \JsonSchema\Validator
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     */
    public function validate(
        \stdClass &$jsonObject = null,
        ServiceInterface $service = null,
        bool $applyDefaultValues = false
    ) {
        if (empty($jsonObject)) {
            throw new ServiceException(ServiceException::MISSING_JSON_STRING);
        }

        if (empty($service->getJsonSchema())) {
            throw new ServiceException(ServiceException::MISSING_SERVICE_SCHEMA);
        }

        $schema = JsonReader::decode(JsonReader::read($this->schemaDir . $service->getJsonSchema()));
        $checkMode = $applyDefaultValues === true ? Constraint::CHECK_MODE_APPLY_DEFAULTS : null;
        $this->validator->validate($jsonObject, $schema, $checkMode);

        return $this->validator;
    }

    /**
     * @return \JsonSchema\Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param \JsonSchema\Validator|null $validator
     * @return \Micronative\ServiceSchema\Service\ServiceValidator
     */
    public function setValidator(Validator $validator = null)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * @return string
     */
    public function getSchemaDir()
    {
        return $this->schemaDir;
    }

    /**
     * @param string|null $schemaDir
     * @return \Micronative\ServiceSchema\Service\ServiceValidator
     */
    public function setSchemaDir(?string $schemaDir = null)
    {
        $this->schemaDir = $schemaDir;

        return $this;
    }
}
