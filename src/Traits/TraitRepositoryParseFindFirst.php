<?php

declare(strict_types=1);

namespace VitesseCms\Database\Traits;

use VitesseCms\Database\AbstractCollection;
use VitesseCms\Database\Models\FindValueIterator;

trait TraitRepositoryParseFindFirst
{
    use TraitRepositoryParseFindValues;

    private function parseFindFirst(
        ?FindValueIterator $findValues = null,
        bool $hideUnpublished = true
    ): ?AbstractCollection {
        $this->class::setFindPublished($hideUnpublished);
        $this->parsefindValues($findValues);

        return $this->class::findFirst();
    }
}