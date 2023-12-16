<?php

declare(strict_types=1);

namespace VitesseCms\Database\Enums;

enum FindValueTypeEnum: string
{
    case NOT = 'not';
    case LIKE = 'like';
    case GREATER_THAN = 'greater';
    case SMALLER_THAN = 'smaller';
    case BETWEEN = 'between';
    case IN_ARRAY = 'in';
}