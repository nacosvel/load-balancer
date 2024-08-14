<?php

namespace Nacosvel\LoadBalancer\Contracts;

interface ArrayObjectInterface
{
    public function toArray(): array;

    public function length(): int;

}
