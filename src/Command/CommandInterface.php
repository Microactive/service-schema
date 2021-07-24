<?php

namespace Micronative\ServiceSchema\Command;

interface CommandInterface
{
    /**
     * @return \Micronative\ServiceSchema\Event\AbstractEvent|bool
     */
    public function execute();
}
