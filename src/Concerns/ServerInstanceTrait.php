<?php

namespace Nacosvel\LoadBalancer\Concerns;

trait ServerInstanceTrait
{
    protected string $scheme   = 'http';
    protected string $user     = '';
    protected string $pass     = '';
    protected string $host     = '';
    protected int    $port     = 0;
    protected string $path     = '/';
    protected string $query    = '';
    protected string $fragment = '';

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function setScheme(string $scheme): static
    {
        $this->scheme = $scheme;
        return $this->buildURI();
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setUser(string $user): static
    {
        $this->user = $user;
        return $this->buildURI();
    }

    public function getPass(): string
    {
        return $this->pass;
    }

    public function setPass(string $pass): static
    {
        $this->pass = $pass;
        return $this->buildURI();
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(string $host): static
    {
        $this->host = $host;
        return $this->buildURI();
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port): static
    {
        $this->port = $port;
        return $this->buildURI();
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;
        return $this->buildURI();
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function setQuery(string $query): static
    {
        $this->query = $query;
        return $this->buildURI();
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function setFragment(string $fragment): static
    {
        $this->fragment = $fragment;
        return $this->buildURI();
    }

    abstract public function getWeight(): float;

    abstract public function setWeight(float $weight): static;

    abstract public function isAlive(): bool;

    abstract public function setAlive(bool $alive): static;

    abstract public function getURI(bool $isBasicAuthentication = false): string;

    abstract public function setURI(string $URI): static;

    abstract public function hashCode(): int;

    abstract protected function buildURI(): static;

}
