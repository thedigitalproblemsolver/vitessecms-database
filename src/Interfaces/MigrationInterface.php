<?php

declare(strict_types=1);

namespace VitesseCms\Database\Interfaces;

interface MigrationInterface
{
    public function up(): bool;
}