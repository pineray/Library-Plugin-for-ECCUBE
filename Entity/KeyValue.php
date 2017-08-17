<?php

namespace Plugin\Lib\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * KeyValue
 */
class KeyValue extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var string
     */
    private $collection;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * Set collection
     *
     * @param string $collection
     * @return KeyValue
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get collection
     *
     * @return string 
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return KeyValue
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return KeyValue
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }
}
