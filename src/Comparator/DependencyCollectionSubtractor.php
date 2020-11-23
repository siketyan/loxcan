<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Comparator;

use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollection;

class DependencyCollectionSubtractor
{
    use DependencyCollectionTrait;

    /**
     * @param DependencyCollection $a
     * @param DependencyCollection $b
     *
     * @return Dependency[]
     */
    public function subtract(DependencyCollection $a, DependencyCollection $b): array
    {
        $packages = $this->getPackages($b);

        return array_values(
            array_filter(
                $a->getDependencies(),
                fn (?Dependency $d): bool => !in_array($d->getPackage(), $packages, true),
            ),
        );
    }
}
