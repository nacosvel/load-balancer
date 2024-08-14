<?php

namespace Nacosvel\LoadBalancer\Contracts;

interface LoadBalancerInterface
{
    public function setServerAddresses(ServerListInterface $serverList): static;

    public function setRule(LoadBalancerRuleInterface $rule): static;

    public function getReachableServers(): ServerListInterface;

    public function getAllServers(): ServerListInterface;

    public function chooseServer(int $key = 0): ServerInstanceInterface;

    public function markServerUp(ServerInstanceInterface $server): ServerInstanceInterface;

    public function markServerDown(ServerInstanceInterface $server): ServerInstanceInterface;

}
