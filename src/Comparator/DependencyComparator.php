<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Comparator;

use Siketyan\Loxcan\Exception\InvalidComparisonException;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyDiff;
use Siketyan\Loxcan\Versioning\VersionComparatorInterface;
use Siketyan\Loxcan\Versioning\VersionComparatorResolver;
use Siketyan\Loxcan\Versioning\VersionDiff;

class DependencyComparator
{
    public function __construct(
        private readonly VersionComparatorResolver $versionComparatorResolver,
    ) {
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

        if (!$versionComparator instanceof VersionComparatorInterface) {
            $versionDiff = new VersionDiff(
                VersionDiff::UNKNOWN,
                $before->getVersion(),
                $after->getVersion(),
            );
        } else {
            $versionDiff = $versionComparator->compare(
                $before->getVersion(),
                $after->getVersion(),
            );
        }

        if (!$versionDiff instanceof VersionDiff) {
            return null;
        }

        return new DependencyDiff(
            $after->getPackage(),
            $versionDiff,
        );
    }
}
