<?php
/**
 * This file is part of the FormHandlerBundle project.
 *
 * @author     pierre
 * @copyright  Copyright (c) 2017
 */

namespace SolidWorx\FormHandler;

class Options implements \ArrayAccess, \Countable, \IteratorAggregate
{
    private $options = [];

    private function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * Get a options, providing a default if the value doesn't exist
     *
     * @param string $key
     * @param null   $default
     *
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        if (array_key_exists($key, $this->options)) {
            return $this->options[$key];
        }

        return $default;
    }

    /**
     * @param array $options
     *
     * @return Options
     */
    public static function fromArray(array $options): Options
    {
        return new self($options);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        throw new \LogicException(__CLASS__." is immutable, you can't change it's values");
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        throw new \LogicException(__CLASS__." is immutable, you can't change it's values");
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->options);
    }

    /**
     * @param array $options
     *
     * @return Options
     */
    public function merge(array $options)
    {
        return new self(array_merge_recursive($options, $this->options));
    }
}