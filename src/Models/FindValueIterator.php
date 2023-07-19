<?php declare(strict_types=1);

namespace VitesseCms\Database\Models;

use ArrayIterator;

class FindValueIterator extends ArrayIterator
{
    public function __construct(array $findValues = [])
    {
        parent::__construct($findValues);
    }

    public function current(): FindValue
    {
        return parent::current();
    }

    public function add(FindValue $findValue): FindValueIterator
    {
        $this->append($findValue);

        return $this;
    }
}
