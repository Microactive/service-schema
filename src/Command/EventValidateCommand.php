<?php

namespace Micronative\ServiceSchema\Command;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Validators\EventValidator;

class EventValidateCommand implements CommandInterface
{
    /** @var \Micronative\ServiceSchema\Validators\EventValidator */
    private $eventValidator;

    /** @var \Micronative\ServiceSchema\Event\AbstractEvent */
    private $event;

    /** @var string */
    private $jsonSchema;

    /** @var bool */
    private $applyDefaultValues;

    /**
     * EventValidateCommand constructor.
     * @param \Micronative\ServiceSchema\Validators\EventValidator $validator
     * @param \Micronative\ServiceSchema\Event\AbstractEvent $event
     * @param string|null $jsonSchema
     * @param bool $applyDefaultValues
     */
    public function __construct(
        EventValidator $validator,
        AbstractEvent $event,
        string $jsonSchema = null,
        bool $applyDefaultValues = false
    ) {
        $this->eventValidator = $validator;
        $this->event = $event;
        $this->jsonSchema = $jsonSchema;
        $this->applyDefaultValues = $applyDefaultValues;
    }

    /**
     * @return bool|\Micronative\ServiceSchema\Event\AbstractEvent|void
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
     */
    public function execute()
    {
        return $this->eventValidator->validateEvent($this->event, $this->jsonSchema, $this->applyDefaultValues);
    }
}
