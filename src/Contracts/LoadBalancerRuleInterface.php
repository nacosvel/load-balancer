<?php

namespace Nacosvel\LoadBalancer\Contracts;

interface LoadBalancerRuleInterface
{
    public function getLoadBalancer(): LoadBalancerInterface;

    public function setLoadBalancer(LoadBalancerInterface $loadBalancer): static;

    public function choose(int $key = 0): ServerInstanceInterface;

}
