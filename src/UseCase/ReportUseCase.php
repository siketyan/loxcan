<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\UseCase;

use Siketyan\Loxcan\Model\DependencyCollectionDiff;
use Siketyan\Loxcan\Reporter\ReporterResolver;

class ReportUseCase
{
    public function __construct(
        private readonly ReporterResolver $reporterResolver,
    ) {
    }

    /**
     * @param array<string, DependencyCollectionDiff> $diffs
     * @param list<string>                            $reporters
     * @param array<string, mixed>                    $context
     */
    public function report(array $diffs, array $reporters, array $context): void
    {
        $reporters = $this->reporterResolver->resolveAll($reporters);

        foreach ($reporters as $reporter) {
            if ($reporter->supports()) {
                $reporter->report($diffs, $context);
            }
        }
    }
}
