<?php

namespace Nacosvel\LoadBalancer\Rules;

use Nacosvel\LoadBalancer\Contracts\LoadBalancerRuleInterface;
use Nacosvel\LoadBalancer\Contracts\ServerInstanceInterface;
use Nacosvel\LoadBalancer\Server\ServerInstance;

class RandomRule extends AbstractLoadBalancerRule implements LoadBalancerRuleInterface
{
    public function choose(int $key = 0): ServerInstanceInterface
    {
        if (array_key_exists($key, ServerInstance::$hashCodeTable) && ServerInstance::$hashCodeTable[$key]->isAlive()) {
            return ServerInstance::$hashCodeTable[$key];
        }

        $reachableServers = $this->getLoadBalancer()->getReachableServers();
        $range            = range(0, $reachableServers->count() - 1, 1);
        $random           = array_rand($range);
        $reachableServers->seek($random);

        assert($reachableServers->current() instanceof ServerInstanceInterface);

        return $reachableServers->current();
    }

}
