<?php

namespace Nacosvel\LoadBalancer\Server;

class ServerList extends AbstractServerList
{
    /**
     * @param string|array $server_addresses
     *
     * @example
     *         $serverList = new ServerList('https://127.0.0.1:8848/nacos');
     *         $serverList = new ServerList('127.0.0.1:8848/nacos,https://127.0.0.1:8848');
     *         $serverList = new ServerList(['127.0.0.1:8848/nacos', 'https://127.0.0.1:8848']);
     *         $serverList = new ServerList(['127.0.0.1:8848/nacos' => 5, 'https://127.0.0.1:8848' => 8.5]);
     */
    public function __construct(string|array $server_addresses = [])
    {
        if (is_string($server_addresses)) {
            $server_addresses = explode(',', $server_addresses);
        }
        $this->exchangeIterator($server_addresses);
    }

}
