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
        $this->setServerAddresses($serverAddresses instanceof ServerIteratorInterface ? $serverAddresses : new ServerIterator($serverAddresses));
        $this->setRule($rule instanceof LoadBalancerRuleInterface ? $rule : new RandomRule());
    }

    /**
     * 服务实例清单更新
     *
     * @param ServerIteratorInterface $servers
     *
     * @return static
     */
    public function setServerAddresses(ServerIteratorInterface $servers): static
    {
        $this->servers = $servers;
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
        $this->loadBalancerRule->setLoadBalancer($this);

        return $this;
    }

}
