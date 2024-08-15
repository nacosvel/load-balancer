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
 * 随机策略
 * 从可用服务列表随机取一个服务实例
 *
 * @meta 适用于集群中各个节点提供服务能力等同且无状态的场景
 */
class RandomRule extends AbstractLoadBalancerRule implements LoadBalancerRuleInterface
{
    /**
     * @throws LoadBalancerExceptions
     */
    public function choose(int $key = 0): ServerInstanceInterface
    {
        if (array_key_exists($key, ServerInstance::$hashCodeTable) && ServerInstance::$hashCodeTable[$key]->isAlive()) {
            return ServerInstance::$hashCodeTable[$key];
        }

        $reachableServers = $this->getLoadBalancer()->getReachableServers();

        assert($reachableServers->count(), new UnderflowExceptions('There are no more reachable servers available'));

        try {
            $reachableServers->seek($this->getRandomNumber($reachableServers->count()));
        } catch (OutOfBoundsException $exception) {
            return $this->choose($key);
        } catch (Exception $exception) {
            throw new LoadBalancerExceptions($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }

        assert($reachableServers->current() instanceof ServerInstanceInterface, new InvalidArgumentExceptions('reachable server instance implements interface error'));

        return $reachableServers->current();
    }

}
