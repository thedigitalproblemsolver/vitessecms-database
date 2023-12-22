<?php

declare(strict_types=1);

namespace VitesseCms\Database\Listeners;

use Phalcon\Events\Event;
use VitesseCms\Database\DTO\GetRepositoryDTO;

class RepositoryListener
{
    public function get(Event $event, GetRepositoryDTO $getRepositoryDTO)
    {
        $class = str_replace(
                ['Models'],
                ['Repositories'],
                $getRepositoryDTO->class
            ) . 'Repository';

        return new $class();
    }
}