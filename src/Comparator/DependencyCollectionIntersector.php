<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Comparator;

use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\DependencyDiff;
use Siketyan\Loxcan\Model\Package;

class DependencyCollectionIntersector
{
    use DependencyCollectionTrait;

    public function __construct(
        private DependencyComparator $comparator,
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

            if ($before === null || $after === null) {
                continue;
            }

            $diff[] = $this->comparator->compare($before, $after);
        }

        return array_filter(
            $diff,
            fn (?DependencyDiff $d) => $d !== null,
        );
    }
}
