<?php

namespace Nacosvel\LoadBalancer\Contracts;

interface ServerIteratorInterface extends ArrayableInterface
{
    public function getReachableServers(): static;

    public function getAllServers(): static;

}
