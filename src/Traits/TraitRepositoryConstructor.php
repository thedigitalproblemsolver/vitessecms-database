<?php

declare(strict_types=1);

namespace VitesseCms\Database\Traits;

trait TraitRepositoryConstructor
{
    private readonly string $classIterator;

    public function __construct(private readonly string $class)
    {
        $this->classIterator = $this->class . 'Iterator';
    }
}
