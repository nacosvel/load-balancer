<?php

namespace Nacosvel\LoadBalancer\Rules;

use Nacosvel\LoadBalancer\Contracts\LoadBalancerRuleInterface;
use Nacosvel\LoadBalancer\Contracts\ServerInstanceInterface;
use Nacosvel\LoadBalancer\Exceptions\InvalidArgumentExceptions;
use Nacosvel\LoadBalancer\Exceptions\UnderflowExceptions;
use Nacosvel\LoadBalancer\Server\ServerInstance;

class RandomRule extends AbstractLoadBalancerRule implements LoadBalancerRuleInterface
{
    public function choose(int $key = 0): ServerInstanceInterface
    {
        if (array_key_exists($key, ServerInstance::$hashCodeTable) && ServerInstance::$hashCodeTable[$key]->isAlive()) {
            return ServerInstance::$hashCodeTable[$key];
        }

        $reachableServers = $this->getLoadBalancer()->getReachableServers();

        assert($reachableServers->count(), new UnderflowExceptions('There are no more reachable servers available'));

        try {
            $reachableServers->seek($this->getRandomNumber($reachableServers->count()));
        } catch (\Throwable $throwable) {
            if (false === $reachableServers->valid()) {
                return $this->choose($key);
            }
        }

        assert($reachableServers->current() instanceof ServerInstanceInterface, new InvalidArgumentExceptions('reachable server instance implements interface error'));

        return $reachableServers->current();
    }

}
