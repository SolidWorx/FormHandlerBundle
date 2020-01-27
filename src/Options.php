<?php

declare(strict_types=1);

/**
 * This file is part of the FormHandler package.
 *
 * (c) SolidWorx <open-source@solidworx.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
     * Get a options, providing a default if the value doesn't exist.
     *
     * @param null $default
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
     * @return Options
     */
    public static function fromArray(array $options): self
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
     * @return Options
     */
    public function merge(array $options)
    {
        return new self(array_merge_recursive($options, $this->options));
    }
}
