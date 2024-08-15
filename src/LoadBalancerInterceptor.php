<?php

namespace Nacosvel\LoadBalancer;

use Nacosvel\LoadBalancer\Contracts\ClientHttpRequestInterface;

class LoadBalancerInterceptor implements ClientHttpRequestInterface
{
    private LoadBalancerClient $loadBalancerClient;

}
