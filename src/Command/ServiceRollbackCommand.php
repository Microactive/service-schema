<?php

namespace Micronative\ServiceSchema\Command;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Service\RollbackInterface;
use Micronative\ServiceSchema\Validators\ServiceValidator;

class ServiceRollbackCommand implements CommandInterface
{
    /** @var \Micronative\ServiceSchema\Validators\ServiceValidator */
    protected $serviceValidator;

    /** @var \Micronative\ServiceSchema\Service\RollbackInterface */
    protected $service;

    /** @var \Micronative\ServiceSchema\Event\AbstractEvent */
    protected $event;

    /**
     * RollbackCommand constructor.
     * @param \Micronative\ServiceSchema\Validators\ServiceValidator $validator
     * @param \Micronative\ServiceSchema\Service\RollbackInterface $service
     * @param \Micronative\ServiceSchema\Event\AbstractEvent $event
     */
    public function __construct(ServiceValidator $validator, RollbackInterface $service, AbstractEvent $event)
    {
        $this->serviceValidator = $validator;
        $this->service = $service;
        $this->event = $event;
    }

    /**
     * @return bool|\Micronative\ServiceSchema\Event\AbstractEvent
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
     */
    public function execute()
    {
        $this->serviceValidator->validateService($this->event, $this->service, false);
        return $this->service->rollback($this->event);
    }
}
