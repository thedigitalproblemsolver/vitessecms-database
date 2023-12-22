<?php

declare(strict_types=1);

namespace VitesseCms\Database\Traits;

trait TraitRepositoryConstructor
{
    private readonly string $classIterator;

    public function __construct(private ?string $class = null)
    {
        if ($class === null) {
            $this->class = str_replace(
                ['Repositories', 'Repository'],
                ['Models', ''],
                self::class
            );
        }
        
        $this->classIterator = $this->class . 'Iterator';
    }
}
