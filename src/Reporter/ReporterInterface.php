<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter;

use Siketyan\Loxcan\Model\DependencyCollectionDiff;

interface ReporterInterface
{
    /**
     * @param array<string, DependencyCollectionDiff> $diffs
     */
    public function report(array $diffs): void;

    public function supports(): bool;
}
