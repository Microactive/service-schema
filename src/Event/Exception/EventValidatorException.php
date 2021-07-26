<?php

namespace Micronative\ServiceSchema\Event\Exception;

use Micronative\ServiceSchema\Exceptions\ServiceSchemaException;

class EventValidatorException extends ServiceSchemaException
{
    const INVALID_JSON = "Event->toJson is invalid Json string.";
    const INVALID_SCHEMA = "Invalid schema provided.";
    const INVALIDATED_EVENT = "Event is not validated by event schema. Error: ";
}
