<?php

namespace Nacosvel\LoadBalancer\Contracts;

interface LoadBalancerInterface
{
    public function setServerAddresses(ServerIteratorInterface $serverIterator): static;

    public function setRule(LoadBalancerRuleInterface $rule): static;

    public function getReachableServers(): ServerIteratorInterface;

    public function getAllServers(): ServerIteratorInterface;

    public function chooseServer(int $key = 0): ServerInstanceInterface;

    public function markServerUp(ServerInstanceInterface $server): ServerInstanceInterface;

    public function markServerDown(ServerInstanceInterface $server): ServerInstanceInterface;

}
