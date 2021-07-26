<?php

namespace Micronative\ServiceSchema\Validators;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;
use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Json\JsonReader;
use Micronative\ServiceSchema\Validators\Exceptions\ValidatorException;

class EventValidator
{
    /** @var \JsonSchema\Validator */
    protected $validator;

    /** @var string */
    protected $schemaDir;

    /**
     * EventValidator constructor.
     * @param string|null $schemaDir
     * @param \JsonSchema\Validator|null $validator
     */
    public function __construct(string $schemaDir = null, Validator $validator = null)
    {
        $this->schemaDir = $schemaDir;
        $this->validator = $validator ?? new  Validator();
    }

    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent $event
     * @param string|null $jsonSchema
     * @param bool $applyDefaultValues
     * @return bool
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
     */
    public function validateEvent(AbstractEvent $event, string $jsonSchema = null, bool $applyDefaultValues = false)
    {
        $jsonObject = JsonReader::decode($event->toJson());
        if (empty($jsonObject)) {
            throw new ValidatorException(ValidatorException::INVALID_JSON);
        }

        $schema = JsonReader::decode(JsonReader::read($this->schemaDir . $jsonSchema));
        if (empty($schema)) {
            throw new ValidatorException(ValidatorException::INVALID_SCHEMA);
        }

        $checkMode = $applyDefaultValues === true ? Constraint::CHECK_MODE_APPLY_DEFAULTS : null;
        $this->validator->validate($jsonObject, $schema, $checkMode);

        if (!$this->validator->isValid()) {
            throw new ValidatorException(
                ValidatorException::INVALIDATED_EVENT . json_encode($this->validator->getErrors())
            );
        }

        if ($applyDefaultValues === true) {
            if (isset($jsonObject->payload)) {
                $event->setPayload($jsonObject->payload);
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function getSchemaDir(): string
    {
        return $this->schemaDir;
    }

    /**
     * @param string $schemaDir
     * @return \Micronative\ServiceSchema\Validators\EventValidator
     */
    public function setSchemaDir(string $schemaDir): EventValidator
    {
        $this->schemaDir = $schemaDir;

        return $this;
    }

    /**
     * @return \JsonSchema\Validator
     */
    public function getValidator(): Validator
    {
        return $this->validator;
    }

    /**
     * @param \JsonSchema\Validator $validator
     * @return \Micronative\ServiceSchema\Validators\EventValidator
     */
    public function setValidator(Validator $validator): EventValidator
    {
        $this->validator = $validator;

        return $this;
    }
}
