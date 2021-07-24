<?php

namespace Micronative\ServiceSchema\Json;

use Micronative\ServiceSchema\Main\Processor;

class SchemaExporter
{

    /** @var \Micronative\ServiceSchema\Main\Processor */
    protected $processor;

    const SCHEMA_EXTENSION = 'json';
    const RETURN_JSON = 1;
    const RETURN_ARRAY = 2;

    /**
     * SchemaReader constructor.
     *
     * @param \Micronative\ServiceSchema\Main\Processor|null $processor
     */
    public function __construct(Processor $processor = null)
    {
        $this->processor = $processor;
    }

    /**
     * @param int $returnType
     * @return array|string
     * @throws \Micronative\ServiceSchema\Json\Exceptions\JsonException
     */
    public function export(int $returnType = self::RETURN_ARRAY)
    {
        $files = [];
        $services = $this->processor->getServiceRegister()->getServices();
        foreach ($services as $service) {
            $files[$service['schema']] = $this->processor->getServiceValidator()->getSchemaDir() . $service['schema'];
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
