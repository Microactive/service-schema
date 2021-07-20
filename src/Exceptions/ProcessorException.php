<?php


namespace Micronative\ServiceSchema\Exceptions;

class ProcessorException extends ServiceSchemaException
{
    const FAILED_TO_CREATE_MESSAGE = "Failed to create message from json string: ";
    const NO_REGISTER_EVENTS = "No registered events for: ";
    const NO_REGISTER_SERVICES = "No registered services for: ";
    const FILTERED_EVENT_ONLY = "Only filtered events are allowed to process: ";
}
