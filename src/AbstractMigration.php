<?php

declare(strict_types=1);

namespace VitesseCms\Database;

use Phalcon\Events\Manager;
use VitesseCms\Cli\Services\TerminalService;
use VitesseCms\Database\Interfaces\MigrationInterface;

abstract class AbstractMigration implements MigrationInterface
{
    public function __construct(
        protected readonly Manager $eventsManager,
        protected readonly TerminalService $terminalService
    ) {
    }
}