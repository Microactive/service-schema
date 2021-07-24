<?php

namespace Micronative\ServiceSchema\Command;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Service\RollbackInterface;
use Micronative\ServiceSchema\Service\ServiceValidator;

class RollbackCommand extends AbstractCommand implements CommandInterface
{
    /**
     * RollbackCommand constructor.
     * @param \Micronative\ServiceSchema\Service\ServiceValidator $validator
     * @param \Micronative\ServiceSchema\Service\RollbackInterface $service
     * @param \Micronative\ServiceSchema\Event\AbstractEvent $event
     */
    public function __construct(ServiceValidator $validator, RollbackInterface $service, AbstractEvent $event)
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
            return $this->service->rollback($this->event);
        }
    }
}
