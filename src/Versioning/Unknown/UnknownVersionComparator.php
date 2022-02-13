<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\Unknown;

use JetBrains\PhpStorm\Pure;
use Siketyan\Loxcan\Versioning\VersionComparatorInterface;
use Siketyan\Loxcan\Versioning\VersionDiff;
use Siketyan\Loxcan\Versioning\VersionInterface;

class UnknownVersionComparator implements VersionComparatorInterface
{
    #[Pure]
    public function compare(VersionInterface $before, VersionInterface $after): ?VersionDiff
    {
        if (((string) $before) === ((string) $after)) {
            return null;
        }

        return new VersionDiff(
            VersionDiff::UNKNOWN,
            $before,
            $after,
        );
    }

    public function supports(string $beforeType, string $afterType): bool
    {
        return $beforeType === UnknownVersion::class && $afterType === UnknownVersion::class;
    }
}
