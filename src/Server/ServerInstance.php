<?php

namespace Nacosvel\LoadBalancer\Server;

use Nacosvel\Utils\Utils;

class ServerInstance extends AbstractServerInstance
{
    public function __construct(
        protected string $URI,
        protected float  $weight = 1.0,
        protected bool   $alive = true
    )
    {
        $this->setURI($this->URI);
    }

    protected function getGenerateURI(bool $isBasicAuthentication = false): string
    {
        return Utils::build_url([
            'scheme'   => $this->getScheme(),
            'pass'     => $this->getPass(),
            'user'     => $this->getUser(),
            'host'     => $this->getHost(),
            'port'     => $this->getPort(),
            'path'     => $this->getPath(),
            'query'    => $this->getQuery(),
            'fragment' => $this->getFragment(),
        ], true, $isBasicAuthentication);
    }

    protected function buildURI(bool $isBasicAuthentication = false): static
    {
        $this->URI = $this->getGenerateURI($isBasicAuthentication);

        return $this->buildHashCode();
    }

    protected function buildHashCode(): static
    {
        if (array_key_exists($this->hashCode, self::$hashCodeTable)) {
            unset(self::$hashCodeTable[$this->hashCode]);
        }

        $this->hashCode = Utils::hashCode(serialize($this));

        self::$hashCodeTable[$this->hashCode] = $this;

        return $this;
    }

}
