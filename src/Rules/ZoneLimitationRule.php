<?php

namespace Nacosvel\LoadBalancer\Rules;

use Exception;
use Nacosvel\LoadBalancer\Contracts\LoadBalancerRuleInterface;
use Nacosvel\LoadBalancer\Contracts\ServerInstanceInterface;
use Nacosvel\LoadBalancer\Exceptions\InvalidArgumentExceptions;
use Nacosvel\LoadBalancer\Exceptions\LoadBalancerExceptions;
use Nacosvel\LoadBalancer\Exceptions\UnderflowExceptions;
use Nacosvel\LoadBalancer\Server\ServerInstance;
use Nacosvel\Utils\Utils;
use OutOfBoundsException;
use Override;

/**
 * 区域权重策略
 * 根据客户端标识进行哈希计算，将同一个客户端标识的请求转发到同一个服务实例
 *
 * @meta 适用于集群中各个节点提供服务能力等同且无状态的场景
 */
class ZoneLimitationRule extends AbstractLoadBalancerRule implements LoadBalancerRuleInterface
{
    protected int $zoneAvoidance = 0;

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
            $reachableServers->seek($this->getRandomNumber($this->getZoneAvoidance(), $reachableServers->count()));
        } catch (OutOfBoundsException $exception) {
            return $this->choose($key);
        } catch (Exception $exception) {
            throw new LoadBalancerExceptions($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }

        assert($reachableServers->current() instanceof ServerInstanceInterface, new InvalidArgumentExceptions('reachable server instance implements interface error'));

        return $reachableServers->current();
    }

    #[Override]
    protected function getRandomNumber(int $max, int $step = 100): int
    {
        if ($step === 0) {
            return 0;
        }
        return fmod($max, $step);
    }

    public function getZoneAvoidance(): int
    {
        if ($this->zoneAvoidance) {
            return $this->zoneAvoidance;
        }
        return $this->zoneAvoidance = Utils::hashCode($this->defaultZoneAvoidance());
    }

    public function setZoneAvoidance(string $zoneAvoidance): ZoneLimitationRule
    {
        $this->zoneAvoidance = Utils::hashCode($zoneAvoidance);
        return $this;
    }

    /**
     * 默认区域 客户端地址
     *
     * @return string
     */
    private function defaultZoneAvoidance(): string
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $_SERVER['REMOTE_ADDR'];
    }

}
