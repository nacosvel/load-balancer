<?php

namespace Nacosvel\LoadBalancer\Contracts;

interface LoadBalancerInterface
{
    public function setServerAddresses(ServerIteratorInterface $servers): static;

    public function setRule(LoadBalancerRuleInterface $rule): static;

    public function getReachableServers(): ServerIteratorInterface;

    public function getAllServers(): ServerIteratorInterface;

    public function chooseServer(int $key = 0): ServerInstanceInterface;

    public function markServerUp(ServerInstanceInterface $instance): ServerInstanceInterface;

    public function markServerDown(ServerInstanceInterface $instance): ServerInstanceInterface;

}
