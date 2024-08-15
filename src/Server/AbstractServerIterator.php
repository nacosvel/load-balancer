<?php

namespace Nacosvel\LoadBalancer\Server;

use ArrayIterator;
use Nacosvel\LoadBalancer\Concerns\ArrayableTrait;
use Nacosvel\LoadBalancer\Contracts\ServerInstanceInterface;
use Nacosvel\LoadBalancer\Contracts\ServerIteratorInterface;

abstract class AbstractServerIterator extends ArrayIterator implements ServerIteratorInterface
{
    use ArrayableTrait;

    /**
     * 获取当前正常服务的实例列表
     *
     * @return static
     */
    public function getReachableServers(): static
    {
        return new static(array_filter($this->getArrayCopy(), function (ServerInstanceInterface $server) {
            return $server->isAlive();
        }));
    }

    /**
     * 获取所有已知的服务实例列表，包括正常服务和停止服务的实例
     *
     * @return static
     */
    public function getAllServers(): static
    {
        return new static($this->getArrayCopy());
    }
}
