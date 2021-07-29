<?php

namespace Micronative\ServiceSchema\Event\Exception;

use Micronative\ServiceSchema\Exceptions\ServiceSchemaException;

class EventException extends ServiceSchemaException
{
    const MISSING_EVENT_NAME = "Event name is missing in json strin.";
}
