<?php

namespace Nacosvel\LoadBalancer\Concerns;

use Nacosvel\LoadBalancer\Contracts\ServerInstanceInterface;
use Nacosvel\LoadBalancer\Server\AbstractServerIterator;
use Nacosvel\LoadBalancer\Server\ServerInstance;
use Override;
use ReturnTypeWillChange;

/**
 * This iterator allows to unset and modify values and keys while iterating over Arrays and Objects.
 *
 * @link https://php.net/manual/en/class.arrayiterator.php
 */
trait ArrayableTrait
{
    /**
     * Set value for an offset
     *
     * @link https://php.net/manual/en/arrayiterator.offsetset.php
     *
     * @param int                     $key   The index to set for.
     * @param ServerInstanceInterface $value The new value to store at the index.
     *
     * @return void
     */
    #[Override]
    public function offsetSet(mixed $key, mixed $value): void
    {
        assert($value instanceof ServerInstanceInterface);
        parent::offsetSet($key, $value);
    }

    /**
     * Append an element
     *
     * @link https://php.net/manual/en/arrayiterator.append.php
     *
     * @param ServerInstanceInterface $value The value to append.
     *
     * @return void
     */
    #[Override]
    public function append(mixed $value): void
    {
        assert($value instanceof ServerInstanceInterface);
        parent::append($value);
    }

    /**
     * Exchange the Iterator for another one.
     *
     * @param AbstractServerIterator|array $serverInstances The new array or object to exchange with the current Iterator.
     *
     * @return ServerInstanceInterface[] the old iterator.
     */
    public function exchangeIterator(AbstractServerIterator|array $serverInstances): array
    {
        if (is_object($serverInstances)) {
            $serverInstances = $serverInstances->getArrayCopy();
        }

        $oldServerInstances = $this->getArrayCopy();

        // To avoid this bug you can call offsetUnset in the for loop
        // @see https://www.php.net/manual/zh/arrayiterator.offsetunset.php
        for ($this->rewind(); $this->valid();) {
            $this->offsetUnset($this->key());
        }

        if (array_is_list($serverInstances)) {
            $serverInstances = $this->createServerInstanceWithoutWeight($serverInstances);
        } else {
            $serverInstances = $this->createServerInstanceWithWeight($serverInstances);
        }

        foreach ($serverInstances as $key => $value) {
            $this->offsetSet($key, $value);
        }

        return $oldServerInstances;
    }

    /**
     * create ServerInstance With Weight
     *
     * @param array $serverInstances
     *
     * @return ServerInstanceInterface[]
     */
    private function createServerInstanceWithWeight(array $serverInstances): array
    {
        return array_map(function ($key, $value) {
            if ($value instanceof ServerInstanceInterface) {
                return $value;
            }
            return new ServerInstance($key, $value);
        }, array_keys($serverInstances), array_values($serverInstances));
    }

    /**
     * create ServerInstance Without Weight
     *
     * @param array $serverInstances
     *
     * @return ServerInstanceInterface[]
     */
    private function createServerInstanceWithoutWeight(array $serverInstances): array
    {
        return array_reduce(array_filter($serverInstances, function ($instance) {
            return is_string($instance) || $instance instanceof ServerInstanceInterface;
        }), function ($carry, $instance) {
            if (is_string($instance)) {
                $instance = new ServerInstance($instance);
            }
            if ($instance instanceof ServerInstanceInterface) {
                $carry[] = $instance;
            }
            return $carry;
        }, []);
    }

    /**
     * Creates a copy of the ArrayIterator.
     *
     * @link https://php.net/manual/en/arrayiterator.getarraycopy.php
     * @return ServerInstanceInterface[] A copy of the array. When the ArrayIterator refers to an object an array of the public properties of that object will be returned.
     *
     */
    public function toArray(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * Get value for an offset
     *
     * @override
     *
     * @link https://php.net/manual/en/arrayiterator.offsetget.php
     *
     * @param mixed $key The offset to get the value from.
     *
     * @return ServerInstanceInterface The value at offset <i>index</i>.
     */
    #[Override]
    #[ReturnTypeWillChange]
    public function offsetGet(mixed $key): ServerInstanceInterface
    {
        return parent::offsetGet($key);
    }

    /**
     * Return current array entry
     *
     * @link https://php.net/manual/en/arrayiterator.current.php
     * @return ServerInstanceInterface The current array entry.
     */
    #[Override]
    #[ReturnTypeWillChange]
    public function current(): ServerInstanceInterface
    {
        return parent::current();
    }

    /**
     * Return current array key
     *
     * @link https://php.net/manual/en/arrayiterator.key.php
     * @return int The key of the current element.
     */
    #[Override]
    #[ReturnTypeWillChange]
    public function key(): int
    {
        return parent::key();
    }

}
