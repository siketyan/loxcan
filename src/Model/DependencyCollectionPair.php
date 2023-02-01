<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

class DependencyCollectionPair
{
    public function __construct(
        private DependencyCollection $before,
        private DependencyCollection $after,
    ) {
    }

    public function getBefore(): DependencyCollection
    {
        return $this->before;
    }

    public function getAfter(): DependencyCollection
    {
        return $this->after;
    }
}
