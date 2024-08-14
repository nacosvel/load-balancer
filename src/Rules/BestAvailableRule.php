<?php

namespace Nacosvel\LoadBalancer\Rules;

use Nacosvel\LoadBalancer\Contracts\LoadBalancerRuleInterface;

class BestAvailableRule extends AbstractLoadBalancerRule implements LoadBalancerRuleInterface
{
    public function choose(int $key = 1): static
    {
        return $this;
    }

}