<?php

namespace Micronative\ServiceSchema\Event;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;
use Micronative\ServiceSchema\Event\Exception\EventValidatorException;
use Micronative\ServiceSchema\Json\JsonReader;

class EventValidator
{
    /** @var \JsonSchema\Validator */
    private $validator;

    /**
     * EventValidator constructor.
     * @param \JsonSchema\Validator|null $validator
     */
    public function __construct(Validator $validator = null)
    {
        $this->validator = $validator ?? new  Validator();
    }

    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent|null $event
     * @param string|null $eventSchema
     * @param bool $applyDefaultValues
     * @return bool
     * @throws \Micronative\ServiceSchema\Event\Exception\EventValidatorException
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     */
    public function validate(AbstractEvent $event = null, ?string $eventSchema = null, bool $applyDefaultValues = false)
    {
        $jsonObject = JsonReader::decode($event->toJson());
        if (empty($jsonObject)) {
            throw new EventValidatorException(EventValidatorException::INVALID_JSON_STRING);
        }

        if (empty($eventSchema)) {
            throw new EventValidatorException(EventValidatorException::MISSING_EVENT_SCHEMA);
        }

        $schema = JsonReader::decode(JsonReader::read($eventSchema));
        $checkMode = $applyDefaultValues === true ? Constraint::CHECK_MODE_APPLY_DEFAULTS : null;
        $this->validator->validate($jsonObject, $schema, $checkMode);

        if (!$this->validator->isValid()) {
            throw new EventValidatorException(
                EventValidatorException::INVALIDATED_EVENT_MESSAGE . json_encode($this->validator->getErrors())
            );
        }

        return $this->validator->isValid();
    }

}
