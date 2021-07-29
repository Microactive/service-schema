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
     * @param bool $applyDefaultValues
     */
    public function __construct(
        EventValidator $validator,
        AbstractEvent $event,
        bool $applyDefaultValues = false
    ) {
        $this->eventValidator = $validator;
        $this->event = $event;
        $this->applyDefaultValues = $applyDefaultValues;
    }

    /**
     * @return bool|\Micronative\ServiceSchema\Event\AbstractEvent|void
     * @throws \Micronative\ServiceSchema\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Exceptions\ValidatorException
     */
    public function execute()
    {
        return $this->eventValidator->validateEvent($this->event, $this->applyDefaultValues);
    }
}
