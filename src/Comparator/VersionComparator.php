<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Comparator;

use Siketyan\Loxcan\Versioning\Version;
use Siketyan\Loxcan\Versioning\VersionDiff;

class VersionComparator
{
    public function compare(Version $before, Version $after): ?VersionDiff
    {
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
