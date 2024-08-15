<?php

namespace Nacosvel\LoadBalancer\Contracts;

interface ServerIteratorInterface extends ArrayIteratorInterface
{
    public function getReachableServers(): static;

    public function getAllServers(): static;

    public function toArray(): array;

}
