<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Comparator;

use Siketyan\Loxcan\Versioning\Version;
use Siketyan\Loxcan\Versioning\VersionComparatorInterface;
use Siketyan\Loxcan\Versioning\VersionDiff;
use Siketyan\Loxcan\Versioning\VersionInterface;

class VersionComparator implements VersionComparatorInterface
{
    public function compare(VersionInterface $before, VersionInterface $after): ?VersionDiff
    {
        if (!($before instanceof Version) || !($after instanceof Version)) {
            return null;
        }

        $type = $this->determineType($before, $after);

        if ($type === null) {
            return null;
        }

        return new VersionDiff(
            $type,
            $before,
            $after,
        );
    }

    public function supports(string $beforeType, string $afterType): bool
    {
        return $beforeType === Version::class
            && $afterType === Version::class
        ;
    }

    private function determineType(Version $before, Version $after): ?int
    {
        if ($before->getMajor() < $after->getMajor()) {
            return VersionDiff::UPGRADED;
        }

        if ($before->getMajor() > $after->getMajor()) {
            return VersionDiff::DOWNGRADED;
        }

        if ($before->getMinor() < $after->getMinor()) {
            return VersionDiff::UPGRADED;
        }

        if ($before->getMinor() > $after->getMinor()) {
            return VersionDiff::DOWNGRADED;
        }

        if ($before->getPatch() < $after->getPatch()) {
            return VersionDiff::UPGRADED;
        }

        if ($before->getPatch() > $after->getPatch()) {
            return VersionDiff::DOWNGRADED;
        }

        if ($before->getRevision() && $after->getRevision()) {
            if ($before->getRevision() < $after->getRevision()) {
                return VersionDiff::UPGRADED;
            }

            if ($before->getRevision() > $after->getRevision()) {
                return VersionDiff::DOWNGRADED;
            }
        }

        return null;
    }
}
