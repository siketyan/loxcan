<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\Composer;

use JetBrains\PhpStorm\Pure;
use Siketyan\Loxcan\Exception\RuntimeException;
use Siketyan\Loxcan\Versioning\VersionComparatorInterface;
use Siketyan\Loxcan\Versioning\VersionDiff;
use Siketyan\Loxcan\Versioning\VersionInterface;

class ComposerVersionComparator implements VersionComparatorInterface
{
    public function compare(VersionInterface $before, VersionInterface $after): ?VersionDiff
    {
        if (!($before instanceof ComposerVersion) || !($after instanceof ComposerVersion)) {
            throw new RuntimeException(
                sprintf(
                    'Type of the version "%s" and "%s" is not supported in the comparator.',
                    get_debug_type($before),
                    get_debug_type($after),
                ),
            );
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
        return $beforeType === ComposerVersion::class && $afterType === ComposerVersion::class;
    }

    #[Pure]
    private function determineType(ComposerVersion $before, ComposerVersion $after): ?int
    {
        if ($before->getX() < $after->getX()) {
            return VersionDiff::UPGRADED;
        }

        if ($before->getX() > $after->getX()) {
            return VersionDiff::DOWNGRADED;
        }

        if ($before->getY() < $after->getY()) {
            return VersionDiff::UPGRADED;
        }

        if ($before->getY() > $after->getY()) {
            return VersionDiff::DOWNGRADED;
        }

        if ($before->getZ() < $after->getZ()) {
            return VersionDiff::UPGRADED;
        }

        if ($before->getZ() > $after->getZ()) {
            return VersionDiff::DOWNGRADED;
        }

        if ($before->getStability() < $after->getStability()) {
            return VersionDiff::UPGRADED;
        }

        if ($before->getStability() > $after->getStability()) {
            return VersionDiff::DOWNGRADED;
        }

        if ($before->getNumber() < $after->getNumber()) {
            return VersionDiff::UPGRADED;
        }

        if ($before->getNumber() > $after->getNumber()) {
            return VersionDiff::DOWNGRADED;
        }

        if ($before->getHash() !== $after->getHash()) {
            return VersionDiff::CHANGED;
        }

        if ($before->getBranch() !== $after->getBranch()) {
            return VersionDiff::UNKNOWN;
        }

        return null;
    }
}
