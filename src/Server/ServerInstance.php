<?php

namespace Nacosvel\LoadBalancer\Server;

use InvalidArgumentException;
use Nacosvel\LoadBalancer\Concerns\ServerInstanceTrait;
use Nacosvel\LoadBalancer\Contracts\ServerInstanceInterface;
use Nacosvel\Utils\Utils;

class ServerInstance implements ServerInstanceInterface
{
    use ServerInstanceTrait;

    protected int $hashCode = 0;

    /**
     * @var ServerInstance[]
     */
    public static array $hashCodeTable = [];

    public function __construct(
        protected string $URI,
        protected float  $weight = 1.0,
        protected bool   $alive = true
    )
    {
        $this->setURI($this->URI);
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): static
    {
        $this->weight = $weight;
        return $this->buildHashCode();
    }

    public function isAlive(): bool
    {
        return $this->alive;
    }

    public function setAlive(bool $alive): static
    {
        $this->alive = $alive;
        return $this->buildHashCode();
    }

    public function getURI(): string
    {
        return $this->URI;
    }

    public function setURI(string $URI): static
    {
        $components = parse_url($URI);

        assert($components || throw new InvalidArgumentException("Invalid URI [{$URI}]"));

        foreach ($components as $key => $value) {
            $this->{$key} = $value;
        }

        return $this->buildURI();
    }

    protected function buildURI(): static
    {
        $this->URI = Utils::build_url([
            'scheme'   => $this->getScheme(),
            'pass'     => $this->getPass(),
            'user'     => $this->getUser(),
            'host'     => $this->getHost(),
            'port'     => $this->getPort(),
            'path'     => $this->getPath(),
            'query'    => $this->getQuery(),
            'fragment' => $this->getFragment(),
        ]);

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

    public function hashCode(): int
    {
        return $this->hashCode;
    }

}
