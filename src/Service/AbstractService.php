<?php

namespace Micronative\ServiceSchema\Service;

use Psr\Container\ContainerInterface;

abstract class AbstractService
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $jsonSchema;

    /** @var \Psr\Container\ContainerInterface */
    protected $container;
    
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    /**
     * @return string
     */
    public function getJsonSchema()
    {
        return $this->jsonSchema;
    }
    
    /**
     * @param string|null $jsonSchema relative path (from Processor::schemaDir) to json schema file
     * @see \Micronative\ServiceSchema\Processor::schemaDir
     * @return \Micronative\ServiceSchema\Service\AbstractService
     */
    public function setJsonSchema(string $jsonSchema = null)
    {
        $this->jsonSchema = $jsonSchema;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @param string|null $name
     * @return \Micronative\ServiceSchema\Service\AbstractService
     */
    public function setName(string $name = null)
    {
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * @return \Psr\Container\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
    
    /**
     * @param \Psr\Container\ContainerInterface|null $container
     * @return AbstractService
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        
        return $this;
    }
}
