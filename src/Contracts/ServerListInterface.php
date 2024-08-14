<?php

namespace Nacosvel\LoadBalancer\Contracts;

interface ServerListInterface
{
    public function getReachableServers(): static;

    public function getAllServers(): static;

}
