<?php

namespace Nacosvel\LoadBalancer\Rules;

use Nacosvel\LoadBalancer\Contracts\LoadBalancerInterface;
use Nacosvel\LoadBalancer\Contracts\LoadBalancerRuleInterface;

abstract class AbstractLoadBalancerRule implements LoadBalancerRuleInterface
{
    protected LoadBalancerInterface $loadBalancer;

    public function getLoadBalancer(): LoadBalancerInterface
    {
        return $this->loadBalancer;
    }

    public function setLoadBalancer(LoadBalancerInterface $loadBalancer): static
    {
        $this->loadBalancer = $loadBalancer;
        return $this;
    }

    abstract public function choose(int $key = 1): static;

}
