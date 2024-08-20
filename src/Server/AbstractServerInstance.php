<?php

namespace Nacosvel\LoadBalancer\Server;

use InvalidArgumentException;
use Nacosvel\LoadBalancer\Concerns\ServerInstanceTrait;
use Nacosvel\LoadBalancer\Contracts\ServerInstanceInterface;

abstract class AbstractServerInstance implements ServerInstanceInterface
{
    use ServerInstanceTrait;

    protected string $URI;
    protected float  $weight   = 1.0;
    protected bool   $alive    = true;
    protected int    $hashCode = 0;

    /**
     * @var ServerInstanceInterface[]
     */
    public static array $hashCodeTable = [];

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

    public function getURI(bool $isBasicAuthentication = false): string
    {
        return $isBasicAuthentication ? $this->getGenerateURI(true) : $this->URI;
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

    public function hashCode(): int
    {
        return $this->hashCode;
    }

    abstract protected function getGenerateURI(bool $isBasicAuthentication = false): string;

    abstract protected function buildURI(bool $isBasicAuthentication = false): static;

    abstract protected function buildHashCode(): static;

}
