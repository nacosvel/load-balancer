<?php

namespace Nacosvel\LoadBalancer\Contracts;

use Iterator;

interface ServerIteratorInterface extends Iterator
{
    public function getReachableServers(): static;

    public function getAllServers(): static;

    public function toArray(): array;

}
