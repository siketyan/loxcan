<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\Unknown;

use Siketyan\Loxcan\Versioning\VersionComparatorInterface;
use Siketyan\Loxcan\Versioning\VersionDiff;
use Siketyan\Loxcan\Versioning\VersionInterface;

class UnknownVersionComparator implements VersionComparatorInterface
{
    public function compare(VersionInterface $before, VersionInterface $after): ?VersionDiff
    {
        return new VersionDiff(
            VersionDiff::UNKNOWN,
            $before,
            $after,
        );
    }

    public function supports(string $beforeType, string $afterType): bool
    {
        return is_a($beforeType, VersionInterface::class, true)
            && is_a($afterType, VersionInterface::class, true)
        ;
    }
}
