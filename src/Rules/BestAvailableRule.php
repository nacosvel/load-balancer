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

/**
 * 最低并发策略
 * 将请求转发到当前连接数最少的服务实例
 *
 * @meta 允许各节点服务能力不相等并且允许波动
 */
class BestAvailableRule extends AbstractLoadBalancerRule implements LoadBalancerRuleInterface
{
    protected static array $activeResponsesPerServer = [];

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
            $reachableServers->seek($this->getAvailableBest($reachableServers));
        } catch (OutOfBoundsException $exception) {
            return $this->choose($key);
        } catch (Exception $exception) {
            throw new LoadBalancerExceptions($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }

        assert($reachableServers->current() instanceof ServerInstanceInterface, new InvalidArgumentExceptions('reachable server instance implements interface error'));

        $this->updateCurrentCounter($reachableServers->current()->hashCode())->updateActiveCounter($reachableServers);

        return $reachableServers->current();
    }

    protected function getAvailableBest(ServerIteratorInterface $reachableServers): int
    {
        if (count(self::$activeResponsesPerServer) == 0) {
            return $this->getRandomNumber($reachableServers->count());
        }

        return $this->getAvailabilityPredicate($reachableServers);
    }

    protected function getAvailabilityPredicate(ServerIteratorInterface $servers): int
    {
        $minValue = min(self::$activeResponsesPerServer);

        $hashCodes = array_filter(self::$activeResponsesPerServer, function ($value) use ($minValue) {
            return $value === $minValue;
        });

        $this->updateCurrentCounter($hashCode = array_rand($hashCodes));

        for ($servers->rewind(); $servers->valid(); $servers->next()) {
            if ($servers->current()->hashCode() == $hashCode) {
                return $servers->key();
            }
        }

        return $this->getAvailabilityPredicate($servers);

    }

    /**
     * 当前服务实例的响应次数累计数
     *
     * @param string $hashCode
     *
     * @return static
     */
    protected function updateCurrentCounter(string $hashCode): static
    {
        if (array_key_exists($hashCode, self::$activeResponsesPerServer)) {
            self::$activeResponsesPerServer[$hashCode]++;
        } else {
            self::$activeResponsesPerServer[$hashCode] = 1;
        }

        return $this;
    }

    /**
     * 所有可用服务实例的响应次数累计数
     *
     * @param ServerIteratorInterface $servers
     *
     * @return static
     */
    protected function updateActiveCounter(ServerIteratorInterface $servers): static
    {
        self::$activeResponsesPerServer = array_reduce($servers->toArray(), function ($responses, ServerInstanceInterface $instance) use ($servers) {
            if (array_key_exists($instance->hashCode(), self::$activeResponsesPerServer)) {
                $responses[$instance->hashCode()] = self::$activeResponsesPerServer[$instance->hashCode()];
            } else {
                $responses[$instance->hashCode()] = 0;
            }
            return $responses;
        }, []);

        return $this;
    }

}
