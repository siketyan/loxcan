<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Comparator;

use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\DependencyCollectionDiff;

class DependencyCollectionComparator
{
    private DependencyCollectionSubtractor $subtractor;
    private DependencyCollectionIntersector $intersector;

    public function __construct(
        DependencyCollectionSubtractor $subtractor,
        DependencyCollectionIntersector $intersector
    ) {
        $this->subtractor = $subtractor;
        $this->intersector = $intersector;
    }

    public function compare(DependencyCollection $before, DependencyCollection $after): DependencyCollectionDiff
    {
        return new DependencyCollectionDiff(
            $this->subtractor->subtract($after, $before),
            $this->intersector->intersect($before, $after),
            $this->subtractor->subtract($before, $after),
        );
    }
}
