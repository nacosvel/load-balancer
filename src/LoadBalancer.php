<?php

namespace Nacosvel\LoadBalancer;

use Nacosvel\LoadBalancer\Contracts\LoadBalancerRuleInterface;
use Nacosvel\LoadBalancer\Contracts\ServerListInterface;
use Nacosvel\LoadBalancer\Rules\RandomRule;
use Nacosvel\LoadBalancer\Server\ServerList;

class LoadBalancer extends AbstractLoadBalancer
{
    public function __construct(
        ServerListInterface|array|string $serverAddresses = [],
        ?LoadBalancerRuleInterface       $rule = null
    )
    {
        $this->serverList       = $serverAddresses instanceof ServerListInterface ? $serverAddresses : new ServerList($serverAddresses);
        $this->loadBalancerRule = $rule instanceof LoadBalancerRuleInterface ? $rule : new RandomRule();
        $this->loadBalancerRule->setLoadBalancer($this);
    }

    /**
     * 服务实例清单更新
     *
     * @param ServerListInterface $serverList
     *
     * @return $this
     */
    public function setServerAddresses(ServerListInterface $serverList): static
    {
        $this->serverList = $serverList;
        return $this;
    }

    /**
     * 定义了负载均衡的处理规则对象
     *
     * @param LoadBalancerRuleInterface $rule
     *
     * @return $this
     */
    public function setRule(LoadBalancerRuleInterface $rule): static
    {
        $this->loadBalancerRule = $rule;
        return $this;
    }

}
