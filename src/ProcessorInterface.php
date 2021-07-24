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
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\CommandException
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     */
    public function process(AbstractEvent $event, array $filteredEvents = null, bool $return = false);

    /**
     * @param string|\Micronative\ServiceSchema\Event\AbstractEvent $event
     * @return bool
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Exceptions\ProcessorException
     */
    public function rollback(AbstractEvent $event);
}
