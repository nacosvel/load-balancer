<?php

namespace Nacosvel\LoadBalancer\Contracts;

interface ServerListInterface extends ArrayableInterface
{
    public function getReachableServers(): static;

    public function getAllServers(): static;

}
