<?php

declare(strict_types=1);

namespace VitesseCms\Database\Listeners;

use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Database\Enums\RepositoryEnum;

final class InitiateListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $injectable): void
    {
        $injectable->eventsManager->attach(RepositoryEnum::LISTENER->value, new RepositoryListener());
    }
}
