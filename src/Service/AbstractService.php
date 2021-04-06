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
     * @param string|null $schema
     * @return AbstractService
     */
    public function setJsonSchema(string $schema = null)
    {
        $this->jsonSchema = $schema;
        
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
     * @return AbstractService
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
