<?php

namespace Nacosvel\LoadBalancer\Rules;

use Exception;
use Nacosvel\LoadBalancer\Contracts\LoadBalancerRuleInterface;
use Nacosvel\LoadBalancer\Contracts\ServerInstanceInterface;
use Nacosvel\LoadBalancer\Exceptions\InvalidArgumentExceptions;
use Nacosvel\LoadBalancer\Exceptions\LoadBalancerExceptions;
use Nacosvel\LoadBalancer\Exceptions\UnderflowExceptions;
use Nacosvel\LoadBalancer\Server\ServerInstance;
use OutOfBoundsException;

/**
 * @deprecated
 */
class WeightedRandomRule extends AbstractLoadBalancerRule implements LoadBalancerRuleInterface
{
    /**
     * @throws LoadBalancerExceptions
     */
    public function choose(int $key = 0): ServerInstance
    {
        $reachableServers = $this->getLoadBalancer()->getReachableServers();

        assert($reachableServers->count(), new UnderflowExceptions('There are no more reachable servers available'));

        $reachableServers->rewind();

        assert($reachableServers->current() instanceof ServerInstanceInterface, new InvalidArgumentExceptions('reachable server instance implements interface error'));

        return $reachableServers->current();
    }

}
