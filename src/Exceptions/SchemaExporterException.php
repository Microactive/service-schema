<?php

namespace Micronative\ServiceSchema\Exceptions;

class SchemaExporterException extends ServiceSchemaException
{
    const INVALID_SCHEMA_DIR = "Provided path is not a valid directory: ";
}
