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

/**
 * @implements \ArrayAccess<string, mixed>
 * @implements \IteratorAggregate<string, mixed>
 * @implements \IteratorAggregate<string, mixed>
 */
class Options implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @var array<string, mixed>
     */
    private $options = [];

    /**
     * @param array<string, mixed> $options
     */
    private function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * Get a options, providing a default if the value doesn't exist.
     *
     * @param null $default
     *
     * @return null|mixed
     */
    public function get(string $key, $default = null)
    {
        if (array_key_exists($key, $this->options)) {
            return $this->options[$key];
        }

        return $default;
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return Options<string, mixed>
     */
    public static function fromArray(array $options): self
    {
        return new self($options);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $offset
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->options);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $offset
     * @param mixed $offset
     */
    public function offsetSet($offset, $value): void
    {
        throw new \LogicException(__CLASS__." is immutable, you can't change it's values");
    }

    /**
     * {@inheritdoc}
     *
     * @param string $offset
     */
    public function offsetUnset($offset): void
    {
        throw new \LogicException(__CLASS__." is immutable, you can't change it's values");
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->options);
    }

    /**
     * @return \ArrayIterator<string, mixed>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->options);
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return Options<string, mixed>
     */
    public function merge(array $options): Options
    {
        return new self(array_merge_recursive($options, $this->options));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->options;
    }
}
