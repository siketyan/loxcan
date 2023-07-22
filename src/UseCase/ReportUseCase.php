<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\UseCase;

use Siketyan\Loxcan\Model\DependencyCollectionDiff;
use Siketyan\Loxcan\Reporter\ReporterInterface;

class ReportUseCase
{
    /**
     * @param list<ReporterInterface> $reporters
     */
    public function __construct(
        private readonly array $reporters,
    ) {
    }

    /**
     * @param array<string, DependencyCollectionDiff> $diffs
     */
    public function report(array $diffs): void
    {
        foreach ($this->reporters as $reporter) {
            if ($reporter->supports()) {
                $reporter->report($diffs);
            }
        }
    }
}
