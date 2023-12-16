<?php

declare(strict_types=1);

namespace VitesseCms\Database\Models;

class FindValue
{
    public function __construct(
        public readonly string $key,
        public readonly mixed $value,
        public readonly string $type = 'string'
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
