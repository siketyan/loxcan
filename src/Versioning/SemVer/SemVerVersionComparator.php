<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\SemVer;

use JetBrains\PhpStorm\Pure;
use Siketyan\Loxcan\Versioning\VersionComparatorInterface;
use Siketyan\Loxcan\Versioning\VersionDiff;
use Siketyan\Loxcan\Versioning\VersionInterface;

class SemVerVersionComparator implements VersionComparatorInterface
{
    #[Pure]
    public function compare(VersionInterface $before, VersionInterface $after): ?VersionDiff
    {
        if (!($before instanceof SemVerVersion) || !($after instanceof SemVerVersion)) {
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
        return $beforeType === SemVerVersion::class && $afterType === SemVerVersion::class;
    }

    #[Pure]
    private function determineType(SemVerVersion $before, SemVerVersion $after): ?int
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

        if ($before->isPreRelease() && !$after->isPreRelease()) {
            return VersionDiff::UPGRADED;
        }

        if (!$before->isPreRelease() && $after->isPreRelease()) {
            return VersionDiff::DOWNGRADED;
        }

        $beforePreRelease = $before->getPreRelease();
        $afterPreRelease = $after->getPreRelease();

        for ($i = 0; $i < max(\count($beforePreRelease), \count($afterPreRelease)); ++$i) {
            $beforeIdentifier = $beforePreRelease[$i] ?? 0;
            $afterIdentifier = $afterPreRelease[$i] ?? 0;

            if ($beforeIdentifier === $afterIdentifier) {
                continue;
            }

            if (\is_int($beforeIdentifier) && \is_int($afterIdentifier)) {
                return $beforeIdentifier < $afterIdentifier ? VersionDiff::UPGRADED : VersionDiff::DOWNGRADED;
            }

            if (\is_string($beforeIdentifier) && \is_string($afterIdentifier)) {
                return strcmp($beforeIdentifier, $afterIdentifier) < 0 ? VersionDiff::UPGRADED : VersionDiff::DOWNGRADED;
            }

            return \is_int($beforeIdentifier) ? VersionDiff::UPGRADED : VersionDiff::DOWNGRADED;
        }

        return null;
    }
}
