<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\Simple;

use JetBrains\PhpStorm\Pure;
use Siketyan\Loxcan\Versioning\VersionComparatorInterface;
use Siketyan\Loxcan\Versioning\VersionDiff;
use Siketyan\Loxcan\Versioning\VersionInterface;

class SimpleVersionComparator implements VersionComparatorInterface
{
    #[Pure]
    public function compare(VersionInterface $before, VersionInterface $after): ?VersionDiff
    {
        if (!($before instanceof SimpleVersion) || !($after instanceof SimpleVersion)) {
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
        return $beforeType === SimpleVersion::class
            && $afterType === SimpleVersion::class
        ;
    }

    #[Pure]
    private function determineType(SimpleVersion $before, SimpleVersion $after): ?int
    {
        /* @noinspection DuplicatedCode */
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
