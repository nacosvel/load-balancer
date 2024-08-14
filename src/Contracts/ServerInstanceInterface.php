<?php

namespace Nacosvel\LoadBalancer\Contracts;

interface ServerInstanceInterface
{
    public function getScheme(): string;

    public function setScheme(string $scheme): static;

    public function getUser(): string;

    public function setUser(string $user): static;

    public function getPass(): string;

    public function setPass(string $pass): static;

    public function getHost(): string;

    public function setHost(string $host): static;

    public function getPort(): int;

    public function setPort(int $port): static;

    public function getPath(): string;

    public function setPath(string $path): static;

    public function getQuery(): string;

    public function setQuery(string $query): static;

    public function getFragment(): string;

    public function setFragment(string $fragment): static;

    public function getWeight(): float;

    public function setWeight(float $weight): static;

    public function isAlive(): bool;

    public function setAlive(bool $alive): static;

    public function getURI(): string;

    public function setURI(string $URI): static;

    public function hashCode(): int;

}
