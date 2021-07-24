<?php

namespace Micronative\ServiceSchema\Config;

class ServiceConfig
{
    /** @var string */
    protected $class;

    /** @var string */
    protected $alias;

    /** @var string */
    protected $schema;

    /** @var string[] */
    protected $callbacks;

    /**
     * ServiceConfig constructor.
     * @param string $class
     * @param string $alias
     * @param string|null $schema
     * @param array|null $callbacks
     */
    public function __construct(string $class, string $alias, string $schema = null, array $callbacks = null)
    {
        $this->class = $class;
        $this->alias = $alias;
        $this->schema = $schema;
        $this->callbacks = $callbacks;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     * @return \Micronative\ServiceSchema\Config\ServiceConfig
     */
    public function setClass(string $class): ServiceConfig
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     * @return \Micronative\ServiceSchema\Config\ServiceConfig
     */
    public function setAlias(string $alias): ServiceConfig
    {
        $this->alias = $alias;

        return $this;
    }


    /**
     * @return string
     */
    public function getSchema(): string
    {
        return $this->schema;
    }

    /**
     * @param string $schema
     * @return \Micronative\ServiceSchema\Config\ServiceConfig
     */
    public function setSchema(string $schema): ServiceConfig
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getCallbacks(): array
    {
        return $this->callbacks;
    }

    /**
     * @param string[] $callbacks
     * @return \Micronative\ServiceSchema\Config\ServiceConfig
     */
    public function setCallbacks(array $callbacks): ServiceConfig
    {
        $this->callbacks = $callbacks;

        return $this;
    }
}
