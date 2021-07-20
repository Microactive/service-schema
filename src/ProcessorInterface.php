<?php

namespace Micronative\ServiceSchema;

use Micronative\ServiceSchema\Event\AbstractEvent;
use Micronative\ServiceSchema\Service\ServiceInterface;

interface ProcessorInterface
{

    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent $event
     * @param array|null $filteredEvents
     * @param bool $return return first service result
     * @return bool
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exception\ServiceException
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     */
    public function process(AbstractEvent $event, array $filteredEvents = null, bool $return = false);

    /**
     * @param string|\Micronative\ServiceSchema\Event\AbstractEvent $event
     * @return bool
     * @throws \Micronative\ServiceSchema\Json\Exception\JsonException
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     */
    public function rollback(AbstractEvent $event);

    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent|null $event
     * @param \Micronative\ServiceSchema\Service\ServiceInterface|null $service
     * @param array|null $callbacks
     * @return mixed
     */
    public function runService(AbstractEvent $event, ServiceInterface $service, array $callbacks = null);

    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent $event
     * @param array|null $callbacks
     * @return mixed
     */
    public function runCallbacks(AbstractEvent $event, array $callbacks = null);
}
