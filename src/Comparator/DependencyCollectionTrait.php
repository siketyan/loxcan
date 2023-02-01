<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Comparator;

use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\Package;

trait DependencyCollectionTrait
{
    /**
     * @return Package[]
     */
    private function getPackages(DependencyCollection $collection): array
    {
        return array_map(
            fn (Dependency $d): Package => $d->getPackage(),
            $collection->getDependencies(),
        );
    }

    private function getDependencyByPackage(DependencyCollection $collection, Package $package): ?Dependency
    {
        $filtered = array_filter(
            $collection->getDependencies(),
            fn (Dependency $d): bool => $d->getPackage() === $package,
        );

        return $filtered[array_key_first($filtered)] ?? null;
    }
}
