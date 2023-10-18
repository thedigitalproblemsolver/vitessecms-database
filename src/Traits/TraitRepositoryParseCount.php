<?php

declare(strict_types=1);

namespace VitesseCms\Database\Traits;

use VitesseCms\Database\Models\FindValueIterator;

trait TraitRepositoryParseCount
{
    use TraitRepositoryParseFindValues;

    private function parseCount(?FindValueIterator $findValues = null, bool $hideUnpublished = true): int
    {
        $this->class::setFindPublished($hideUnpublished);
        $this->class::setFindLimit(9999);
        $this->parsefindValues($findValues);

        return $this->class::count();
    }
}