<?php

namespace Nacosvel\LoadBalancer\Contracts;

use SeekableIterator;

interface ArrayableInterface extends SeekableIterator
{
    public function toArray(): array;

}
