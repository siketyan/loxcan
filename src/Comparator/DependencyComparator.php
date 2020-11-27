<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Comparator;

use Siketyan\Loxcan\Exception\InvalidComparisonException;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyDiff;
use Siketyan\Loxcan\Versioning\VersionComparatorResolver;

class DependencyComparator
{
    private VersionComparatorResolver $versionComparatorResolver;

    public function __construct(
        VersionComparatorResolver $versionComparatorResolver
    ) {
        $this->versionComparatorResolver = $versionComparatorResolver;
    }

    public function compare(Dependency $before, Dependency $after): ?DependencyDiff
    {
        if ($before->getPackage() !== $after->getPackage()) {
            throw new InvalidComparisonException(
                'Cannot compare dependencies of different packages.',
            );
        }

        $versionComparator = $this->versionComparatorResolver->resolve(
            $before->getVersion(),
            $after->getVersion(),
        );

        if ($versionComparator === null) {
            throw new InvalidComparisonException(
                sprintf(
                    'No comparator supports to compare versions between "%s" and "%s".',
                    get_debug_type($before->getVersion()),
                    get_debug_type($after->getVersion()),
                ),
            );
        }

        $versionDiff = $versionComparator->compare(
            $before->getVersion(),
            $after->getVersion(),
        );

        if ($versionDiff === null) {
            return null;
        }

        return new DependencyDiff(
            $after->getPackage(),
            $versionDiff,
        );
    }
}
