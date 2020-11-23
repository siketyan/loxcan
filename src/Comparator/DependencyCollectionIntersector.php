<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Comparator;

use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\DependencyDiff;
use Siketyan\Loxcan\Model\Package;

class DependencyCollectionIntersector
{
    use DependencyCollectionTrait;

    private DependencyComparator $comparator;

    public function __construct(
        DependencyComparator $comparator
    ) {
        $this->comparator = $comparator;
    }

    /**
     * @param DependencyCollection $a
     * @param DependencyCollection $b
     *
     * @return DependencyDiff[]
     */
    public function intersect(DependencyCollection $a, DependencyCollection $b): array
    {
        $diff = [];
        $packages = array_uintersect(
            $this->getPackages($a),
            $this->getPackages($b),
            fn (Package $a, Package $b): int => $a === $b ? 0 : -1,
        );

        foreach ($packages as $package) {
            $before = $this->getDependencyByPackage($a, $package);
            $after = $this->getDependencyByPackage($b, $package);

            if ($before === null || $after === null) {
                continue;
            }

            $diff[] = $this->comparator->compare($before, $after);
        }

        return $diff;
    }
}
