<?php

namespace Micronative\ServiceSchema\Config\Exceptions;

use Micronative\ServiceSchema\Exceptions\ServiceSchemaException;

class ConfigException extends ServiceSchemaException
{
    const MISSING_EVENT_CONFIGS = "Event configs are missing.";
    const MISSING_SERVICE_CONFIGS = "Service configs are missing.";
    const UNSUPPORTED_FILE_FORMAT = "Only support JSON and YAML. Provided file: ";
}
