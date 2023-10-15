<?php

declare(strict_types=1);

namespace VitesseCms\Database\Traits;

use VitesseCms\Database\Models\FindValueIterator;

trait TraitRepositoryParseFindValues
{
    private function parseFindValues(?FindValueIterator $findValues = null): void
    {
        if ($findValues !== null) :
            while ($findValues->valid()) :
                $findValue = $findValues->current();
                $this->class::setFindValue(
                    $findValue->getKey(),
                    $findValue->getValue(),
                    $findValue->getType()
                );
                $findValues->next();
            endwhile;
        endif;
    }
}