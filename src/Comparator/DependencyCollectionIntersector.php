<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Comparator;

use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\DependencyDiff;
use Siketyan\Loxcan\Model\Package;

class DependencyCollectionIntersector
{
    use DependencyCollectionTrait;

    public function __construct(
        private readonly DependencyComparator $comparator,
    ) {
    }

    /**
     * @return DependencyDiff[]
     */
    public function intersect(DependencyCollection $a, DependencyCollection $b): array
    {
        $diff = [];
        $packages = array_uintersect(
            $this->getPackages($a),
            $this->getPackages($b),
            fn (Package $u, Package $v): int => spl_object_id($u) - spl_object_id($v),
        );

        foreach ($packages as $package) {
            $before = $this->getDependencyByPackage($a, $package);
            $after = $this->getDependencyByPackage($b, $package);

            if (!$before instanceof Dependency || !$after instanceof Dependency) {
                continue;
            }

            $diff[] = $this->comparator->compare($before, $after);
        }

        return array_filter(
            $diff,
            fn (?DependencyDiff $d): bool => $d instanceof DependencyDiff,
        );
    }
}
