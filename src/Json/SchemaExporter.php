<?php

namespace Micronative\ServiceSchema\Json;

use Micronative\ServiceSchema\Processor;

class SchemaExporter
{

    /** @var \Micronative\ServiceSchema\Processor */
    protected $processor;

    const SCHEMA_EXTENSION = 'json';
    const RETURN_JSON = 1;
    const RETURN_ARRAY = 2;

    /**
     * SchemaReader constructor.
     *
     * @param \Micronative\ServiceSchema\Processor|null $processor
     */
    public function __construct(Processor $processor = null)
    {
        $this->processor = $processor;
    }

    /**
     * @param int $returnType
     * @return array|string
     * @throws \Micronative\ServiceSchema\Exceptions\JsonException
     */
    public function export(int $returnType = self::RETURN_ARRAY)
    {
        $files = [];
        $serviceConfigs = $this->processor->getServiceConfigRegister()->getServiceConfigs();
        foreach ($serviceConfigs as $config) {
            $files[$config->getSchema()] = $this->processor->getServiceValidator()->getSchemaDir() . $config->getSchema();
        }

        $schemas = [];
        foreach ($files as $file) {
            $schemas[basename($file, '.' . self::SCHEMA_EXTENSION)] = JsonReader::decode(JsonReader::read($file), true);
        }

        switch ($returnType) {
            case self::RETURN_JSON:
                return JsonReader::encode($schemas);
            case self::RETURN_ARRAY:
            default:
                return $schemas;
        }
    }
}
