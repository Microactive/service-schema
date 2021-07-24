<?php

namespace Micronative\ServiceSchema\Command;

use Micronative\ServiceSchema\Json\JsonReader;
use Micronative\ServiceSchema\Service\Exceptions\ServiceException;

class AbstractCommand
{
    /** @var \Micronative\ServiceSchema\Service\ServiceInterface|\Micronative\ServiceSchema\Service\RollbackInterface */
    protected $service;

    /** @var \Micronative\ServiceSchema\Event\AbstractEvent */
    protected $event;

    /** @var \Micronative\ServiceSchema\Service\ServiceValidator */
    protected $validator;

    /**
     * @return bool
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
     */
    protected function validate()
    {
        $json = JsonReader::decode($this->event->toJson());
        if (isset($json->payload)) {
            $this->event->setPayload($json->payload);
        }

        if (empty($this->service->getJsonSchema())) {
            return true;
        }

        $validator = $this->validator->validate($json, $this->service);
        if (!$validator->isValid()) {
            throw  new ServiceException(
                sprintf(
                    ServiceException::INVALIDATED_JSON_STRING,
                    $this->service->getJsonSchema(),
                    json_encode($validator->getErrors())
                )
            );
        }

        return true;
    }
}
