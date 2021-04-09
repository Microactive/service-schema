<?php

namespace Micronative\ServiceSchema\Service;

use Micronative\ServiceSchema\Event\AbstractEvent;

interface RollbackInterface extends ServiceInterface
{

    /**
     * @param \Micronative\ServiceSchema\Event\AbstractEvent|null $event
     * @return \Micronative\ServiceSchema\Event\AbstractEvent|bool
     */
    public function rollback(AbstractEvent $event = null);

}
