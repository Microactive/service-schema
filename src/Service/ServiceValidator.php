<?php

namespace Micronative\ServiceSchema\Service;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;
use Micronative\ServiceSchema\Json\JsonReader;
use Micronative\ServiceSchema\Service\Exception\ServiceException;

class ServiceValidator
{
    /** @var \JsonSchema\Validator */
    protected $validator;

    /** @var string */
    protected $schemaDir;

    /**
     * EventValidator constructor.
     *
     * @param \JsonSchema\Validator|null $validator
     * @param string|null $schemaDir
     */
    public function __construct(Validator $validator = null, string $schemaDir = null)
    {
        $this->validator = $validator ?? new Validator();
        $this->schemaDir = $schemaDir;
    }

    /**
     * @param \stdClass|null $jsonObject
     * @param \Micronative\ServiceSchema\Service\ServiceInterface|null $service
     * @return \JsonSchema\Validator
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     */
    public function validate(\stdClass &$jsonObject = null, ServiceInterface $service = null)
    {
        if (empty($jsonObject)) {
            throw new ServiceException(ServiceException::MISSING_JSON_STRING);
        }

        if (empty($service->getJsonSchema())) {
            throw new ServiceException(ServiceException::MISSING_SERVICE_SCHEMA);
        }

        $schema = JsonReader::decode(JsonReader::read($this->schemaDir . $service->getJsonSchema()));
        $this->validator->validate($jsonObject, $schema, Constraint::CHECK_MODE_APPLY_DEFAULTS);

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
