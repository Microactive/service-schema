<?php

namespace Micronative\ServiceSchema;

use Micronative\ServiceSchema\Event\AbstractEvent;

interface ProcessorInterface
{
    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent $event
     * @param array|null $filteredEvents
     * @param bool $return return first service result
     * @return bool
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     * @throws \Micronative\ServiceSchema\Service\Exceptions\ServiceException
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

    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent $event
     * @param string|null $schemaFile
     * @param bool $applyDefaultValues
     * @return bool|void
     */
    public function validate(AbstractEvent $event, string $schemaFile = null, bool $applyDefaultValues = false);
}
