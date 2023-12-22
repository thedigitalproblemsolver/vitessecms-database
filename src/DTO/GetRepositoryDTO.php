<?php

declare(strict_types=1);

namespace VitesseCms\Database\DTO;

final class GetRepositoryDTO
{
    public function __construct(public readonly string $class)
    {
    }
}