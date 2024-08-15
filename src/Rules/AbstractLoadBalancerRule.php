<?php

namespace Nacosvel\LoadBalancer\Rules;

use Nacosvel\LoadBalancer\Contracts\LoadBalancerInterface;
use Nacosvel\LoadBalancer\Contracts\LoadBalancerRuleInterface;
use Nacosvel\LoadBalancer\Contracts\ServerInstanceInterface;

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

    abstract public function choose(int $key = 0): ServerInstanceInterface;

    /**
     * 根据负载均衡算法，获取随机数
     *
     * @param int $max  上限
     * @param int $step 步长
     *
     * @return int
     *
     * @example getRandomNumber(5, 100): 0|1|2|3|4
     */
    protected function getRandomNumber(int $max, int $step = 100): int
    {
        if ($max === 0) {
            return 0;
        }
        return intdiv(mt_rand(0, $max * $step - 1), $step);
    }

}
