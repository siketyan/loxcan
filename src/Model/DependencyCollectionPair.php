<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

class DependencyCollectionPair
{
    private DependencyCollection $before;
    private DependencyCollection $after;

    public function __construct(
        DependencyCollection $before,
        DependencyCollection $after
    ) {
        $this->before = $before;
        $this->after = $after;
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
