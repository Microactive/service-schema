<?php

namespace Micronative\ServiceSchema\Validators;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;
use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Json\JsonReader;
use Micronative\ServiceSchema\Validators\Exceptions\ValidatorException;

use function Webmozart\Assert\Tests\StaticAnalysis\string;

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
     * @param bool $applyDefaultValues
     * @return bool
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
     */
    public function validateEvent(AbstractEvent $event, bool $applyDefaultValues = false)
    {
        if (empty($schemaFile = $event->getSchema())) {
            return true;
        }

        if (empty($jsonObject = JsonReader::decode($event->jsonSerialize()))) {
            throw new ValidatorException(ValidatorException::INVALID_JSON);
        }

        if (empty($jsonSchema = JsonReader::decode(JsonReader::read($this->schemaDir . $schemaFile)))) {
            throw new ValidatorException(ValidatorException::INVALID_SCHEMA);
        }

        $checkMode = $applyDefaultValues === true ? Constraint::CHECK_MODE_APPLY_DEFAULTS : null;
        $this->validator->validate($jsonObject, $jsonSchema, $checkMode);

        if (!$this->validator->isValid()) {
            throw new ValidatorException(
                ValidatorException::INVALIDATED_EVENT . json_encode($this->validator->getErrors())
            );
        }

        if ($applyDefaultValues === true) {
            $event->jsonUnserialize(json_encode($jsonObject));
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
