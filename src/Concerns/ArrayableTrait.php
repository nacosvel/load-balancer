<?php

namespace Nacosvel\LoadBalancer\Concerns;

use Nacosvel\LoadBalancer\Contracts\ServerInstanceInterface;
use Nacosvel\LoadBalancer\Server\AbstractServerList;
use Nacosvel\LoadBalancer\Server\ServerInstance;

/**
 * This class allows objects to work as arrays.
 *
 * @link https://php.net/manual/en/class.arrayobject.php
 * @template TKey
 * @template TValue
 */
trait ArrayableTrait
{

    /**
     * Sets the value at the specified index to newval
     *
     * @link https://php.net/manual/en/arrayobject.offsetset.php
     *
     * @param TKey   $key   The index being set.
     * @param TValue $value The new value for the <i>index</i>.
     *
     * @return void
     */
    public function offsetSet(mixed $key, mixed $value): void
    {
        assert($value instanceof ServerInstanceInterface);
        parent::offsetSet($key, $value);
    }

    /**
     * Appends the value
     *
     * @link https://php.net/manual/en/arrayobject.append.php
     *
     * @param TValue $value The value being appended.
     *
     * @return void
     */
    public function append(mixed $value): void
    {
        assert($value instanceof ServerInstanceInterface);
        parent::append($value);
    }

    /**
     * Exchange the array for another one.
     *
     * @link https://php.net/manual/en/arrayobject.exchangearray.php
     *
     * @param mixed $array The new array or object to exchange with the current array.
     *
     * @return array the old array.
     */
    public function exchangeArray(object|array $array): array
    {
        assert(is_array($array) || $array instanceof AbstractServerList);

        if (is_object($array)) {
            return parent::exchangeArray($array->toArray());
        }

        $array = array_is_list($array) ? $this->createServerInstanceWithoutWeight($array) : $this->createServerInstanceWithWeight($array);

        return parent::exchangeArray($array);
    }

    /**
     * create ServerInstance With Weight
     *
     * @param array $serverInstances
     *
     * @return array
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
     * @return array
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
     * Creates a copy of the ArrayObject.
     *
     * @link https://php.net/manual/en/arrayobject.getarraycopy.php
     * @return array a copy of the array. When the <b>ArrayObject</b> refers to an object
     * an array of the public properties of that object will be returned.
     */
    public function toArray(): array
    {
        return $this->getArrayCopy();
    }

}
