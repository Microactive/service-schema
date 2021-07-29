<?php

namespace Micronative\ServiceSchema\Validators;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Service\ServiceInterface;

class ServiceValidator extends EventValidator
{
    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent $event
     * @param \Micronative\ServiceSchema\Service\ServiceInterface $service
     * @param bool $applyPayloadDefaultValues
     * @return bool
     * @throws \Micronative\ServiceSchema\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Exceptions\ValidatorException
     */
    public function validateService(AbstractEvent $event, ServiceInterface $service, bool $applyPayloadDefaultValues = false)
    {
        if (empty($schema = $service->getSchema())) {
            return true;
        }
        $event->setSchema($schema);

        return $this->validateEvent($event, $applyPayloadDefaultValues);
    }
}
