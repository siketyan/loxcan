<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Comparator;

use Siketyan\Loxcan\Exception\InvalidComparisonException;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyDiff;

class DependencyComparator
{
    private VersionComparator $versionComparator;

    public function __construct(
        VersionComparator $versionComparator
    ) {
        $this->versionComparator = $versionComparator;
    }

    public function compare(Dependency $before, Dependency $after): ?DependencyDiff
    {
        if ($before->getPackage() !== $after->getPackage()) {
            throw new InvalidComparisonException(
                'Cannot compare dependencies of different packages.',
            );
        }

        $versionDiff = $this->versionComparator->compare(
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
