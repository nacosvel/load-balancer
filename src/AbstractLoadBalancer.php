<?php

namespace Nacosvel\LoadBalancer;

use Nacosvel\LoadBalancer\Contracts\LoadBalancerInterface;
use Nacosvel\LoadBalancer\Contracts\LoadBalancerRuleInterface;
use Nacosvel\LoadBalancer\Contracts\ServerInstanceInterface;
use Nacosvel\LoadBalancer\Contracts\ServerIteratorInterface;

abstract class AbstractLoadBalancer implements LoadBalancerInterface
{
    protected ServerIteratorInterface   $serverIterator;
    protected ServerInstanceInterface   $server;
    protected LoadBalancerRuleInterface $loadBalancerRule;

    abstract public function setServerAddresses(ServerIteratorInterface $serverIterator): static;

    abstract public function setRule(LoadBalancerRuleInterface $rule): static;

    /**
     * 获取当前正常服务的实例列表
     *
     * @return ServerIteratorInterface
     */
    public function getReachableServers(): ServerIteratorInterface
    {
        return $this->serverIterator->getReachableServers();
    }

    /**
     * 获取所有已知的服务实例列表，包括正常服务和停止服务的实例
     *
     * @return ServerIteratorInterface
     */
    public function getAllServers(): ServerIteratorInterface
    {
        return $this->serverIterator->getAllServers();
    }

    /**
     * 通过某种策略，从负载均衡器中挑选出一个具体实例
     *
     * @param int $key server instance hashCode 仅在指定服务实例返回时有效
     *
     * @return ServerInstanceInterface
     */
    public function chooseServer(int $key = 0): ServerInstanceInterface
    {
        return $this->server = $this->loadBalancerRule->choose($key);
    }

    /**
     * 标记某个服务实例正常服务
     *
     * @param ServerInstanceInterface $server
     *
     * @return ServerInstanceInterface
     */
    public function markServerUp(ServerInstanceInterface $server): ServerInstanceInterface
    {
        return $server->setAlive(true);
    }

    /**
     * 标记某个服务实例暂停服务
     *
     * @param ServerInstanceInterface $server
     *
     * @return ServerInstanceInterface
     */
    public function markServerDown(ServerInstanceInterface $server): ServerInstanceInterface
    {
        return $server->setAlive(false);
    }

}
