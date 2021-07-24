<?php

namespace Micronative\ServiceSchema\Command;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Service\ServiceInterface;
use Micronative\ServiceSchema\Service\ServiceValidator;

class ConsumeCommand extends AbstractCommand implements CommandInterface
{
    /**
     * ConsumeCommand constructor.
     * @param \Micronative\ServiceSchema\Service\ServiceValidator $validator
     * @param \Micronative\ServiceSchema\Service\ServiceInterface $service
     * @param \Micronative\ServiceSchema\Event\AbstractEvent $event
     */
    public function __construct(ServiceValidator $validator, ServiceInterface $service, AbstractEvent $event)
    {
        $this->validator = $validator;
        $this->service = $service;
        $this->event = $event;
    }

    /**
     * @return bool|\Micronative\ServiceSchema\Event\AbstractEvent
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     */
    public function execute()
    {
        if ($this->validate()) {
            return $this->service->consume($this->event);
        }
    }
}
