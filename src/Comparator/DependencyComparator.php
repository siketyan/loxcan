<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Comparator;

use Siketyan\Loxcan\Exception\InvalidComparisonException;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyDiff;

class DependencyComparator
{
    public function compare(Dependency $before, Dependency $after): DependencyDiff
    {
        if ($before->getPackage() !== $after->getPackage()) {
            throw new InvalidComparisonException(
                'Cannot compare dependencies of different packages.',
            );
        }

        return new DependencyDiff(
            $after->getPackage(),
            $before->getVersion(),
            $after->getVersion(),
        );
    }
}
