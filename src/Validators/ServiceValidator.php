<?php

namespace Micronative\ServiceSchema\Validators;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Service\ServiceInterface;

class ServiceValidator extends EventValidator
{
    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent $event
     * @param \Micronative\ServiceSchema\Service\ServiceInterface $service
     * @param bool $applyDefaultValues
     * @return bool
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Validators\Exceptions\ValidatorException
     */
    public function validateService(AbstractEvent $event, ServiceInterface $service, bool $applyDefaultValues = false)
    {
        if (empty($jsonSchema = $service->getJsonSchema())) {
            return true;
        }
        $event->setSchema($jsonSchema);

        return $this->validateEvent($event, $applyDefaultValues);
    }
}
