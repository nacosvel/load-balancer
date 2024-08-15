<?php

namespace Nacosvel\LoadBalancer\Rules;

use Exception;
use Nacosvel\LoadBalancer\Contracts\LoadBalancerRuleInterface;
use Nacosvel\LoadBalancer\Contracts\ServerInstanceInterface;
use Nacosvel\LoadBalancer\Contracts\ServerIteratorInterface;
use Nacosvel\LoadBalancer\Exceptions\InvalidArgumentExceptions;
use Nacosvel\LoadBalancer\Exceptions\LoadBalancerExceptions;
use Nacosvel\LoadBalancer\Exceptions\UnderflowExceptions;
use Nacosvel\LoadBalancer\Server\ServerInstance;
use OutOfBoundsException;

class RoundRule extends AbstractLoadBalancerRule implements LoadBalancerRuleInterface
{
    private static int $nextTick     = -1;
    private static int $prevHashCode = 0;

    /**
     * @throws LoadBalancerExceptions
     */
    public function choose(int $key = 1): ServerInstanceInterface
    {
        if (array_key_exists($key, ServerInstance::$hashCodeTable) && ServerInstance::$hashCodeTable[$key]->isAlive()) {
            return ServerInstance::$hashCodeTable[$key];
        }

        $reachableServers = $this->getLoadBalancer()->getReachableServers();

        assert($reachableServers->count(), new UnderflowExceptions('There are no more reachable servers available'));

        try {
            $offset = $this->getNextTick($reachableServers, $reachableServers->count());
            $reachableServers->seek($offset);
        } catch (OutOfBoundsException $exception) {
            return $this->choose($key);
        } catch (Exception $exception) {
            throw new LoadBalancerExceptions($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }

        assert($reachableServers->current() instanceof ServerInstanceInterface, new InvalidArgumentExceptions('reachable server instance implements interface error'));

        if (false === array_key_exists(self::$prevHashCode, ServerInstance::$hashCodeTable)) {
            self::$prevHashCode = $reachableServers->current()->hashCode();
        }

        return $reachableServers->current();
    }

    private function getNextTick(ServerIteratorInterface $reachableServers, int $max = 0): int
    {
        if (self::$nextTick === -1) {
            return self::$nextTick = $this->getRandomNumber($max);
        }

        if (array_key_exists(self::$prevHashCode, ServerInstance::$hashCodeTable)) {
            self::$nextTick++;
        }

        if (self::$nextTick >= $max) {
            $reachableServers->rewind();
            self::$nextTick = $reachableServers->key();
        }

        return self::$nextTick;
    }

}
