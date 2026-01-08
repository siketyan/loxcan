<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\Composer;

use Composer\Semver\Comparator;
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

    private function determineType(ComposerVersion $before, ComposerVersion $after): ?int
    {
        $beforeNormalized = $before->getNormalized();
        $afterNormalized = $after->getNormalized();

        if (Comparator::lessThan($beforeNormalized, $afterNormalized)) {
            return VersionDiff::UPGRADED;
        }

        if (Comparator::greaterThan($beforeNormalized, $afterNormalized)) {
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
