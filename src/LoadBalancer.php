<?php

namespace Nacosvel\LoadBalancer;

use Nacosvel\LoadBalancer\Contracts\LoadBalancerRuleInterface;
use Nacosvel\LoadBalancer\Contracts\ServerIteratorInterface;
use Nacosvel\LoadBalancer\Rules\RandomRule;
use Nacosvel\LoadBalancer\Server\ServerIterator;

class LoadBalancer extends AbstractLoadBalancer
{
    public function __construct(
        ServerIteratorInterface|array|string $serverAddresses = [],
        ?LoadBalancerRuleInterface           $rule = null
    )
    {
        $this->serverIterator   = $serverAddresses instanceof ServerIteratorInterface ? $serverAddresses : new ServerIterator($serverAddresses);
        $this->loadBalancerRule = $rule instanceof LoadBalancerRuleInterface ? $rule : new RandomRule();
        $this->loadBalancerRule->setLoadBalancer($this);
    }

    /**
     * 服务实例清单更新
     *
     * @param ServerIteratorInterface $serverIterator
     *
     * @return static
     */
    public function setServerAddresses(ServerIteratorInterface $serverIterator): static
    {
        $this->serverIterator = $serverIterator;
        return $this;
    }

    /**
     * 定义了负载均衡的处理规则对象
     *
     * @param LoadBalancerRuleInterface $rule
     *
     * @return static
     */
    public function setRule(LoadBalancerRuleInterface $rule): static
    {
        $this->loadBalancerRule = $rule;
        return $this;
    }

}
