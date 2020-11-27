<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning;

interface VersionComparatorInterface
{
    public function compare(VersionInterface $before, VersionInterface $after): ?VersionDiff;
    public function supports(string $beforeType, string $afterType): bool;
}
