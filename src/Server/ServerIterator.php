<?php

namespace Nacosvel\LoadBalancer\Server;

class ServerIterator extends AbstractServerIterator
{
    /**
     * @param string|array $serverAddresses
     *
     * @example
     *         $serverIterator = new ServerIterator('https://127.0.0.1:8848/nacos');
     *         $serverIterator = new ServerIterator('127.0.0.1:8848/nacos,https://127.0.0.1:8848');
     *         $serverIterator = new ServerIterator(['127.0.0.1:8848/nacos', 'https://127.0.0.1:8848']);
     *         $serverIterator = new ServerIterator(['127.0.0.1:8848/nacos' => 5, 'https://127.0.0.1:8848' => 8.5]);
     *         $serverIterator = new ServerIterator($serverIterator);
     */
    public function __construct(string|array $serverAddresses = [])
    {
        if (is_string($serverAddresses)) {
            $serverAddresses = explode(',', $serverAddresses);
        }
        $this->exchangeIterator($serverAddresses);
    }

}
