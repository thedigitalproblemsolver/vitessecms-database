<?php

declare(strict_types=1);

namespace VitesseCms\Database\Enums;

enum RepositoryEnum: string
{
    case LISTENER = 'repository';
    case GET_REPOSITORY = 'repository:get';
}