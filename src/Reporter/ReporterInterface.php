<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter;

use Siketyan\Loxcan\Model\DependencyCollectionDiff;

interface ReporterInterface
{
    /**
     * @param array<string, DependencyCollectionDiff> $diffs
     * @param array<string, mixed>                    $context
     */
    public function report(array $diffs, array $context = []): void;

    public function supports(): bool;

    public function name(): string;
}
