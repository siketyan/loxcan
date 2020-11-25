<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\UseCase;

use Siketyan\Loxcan\Comparator\DependencyCollectionComparator;
use Siketyan\Loxcan\Git\Git;
use Siketyan\Loxcan\Model\DependencyCollectionDiff;
use Siketyan\Loxcan\Model\FileDiff;
use Siketyan\Loxcan\Model\Repository;
use Siketyan\Loxcan\Scanner\ScannerResolver;

class ScanUseCase
{
    private Git $git;
    private ScannerResolver $scannerResolver;
    private DependencyCollectionComparator $comparator;

    public function __construct(
        Git $git,
        ScannerResolver $scannerResolver,
        DependencyCollectionComparator $comparator
    ) {
        $this->git = $git;
        $this->scannerResolver = $scannerResolver;
        $this->comparator = $comparator;
    }

    /**
     * @param Repository $repository
     * @param string     $base
     * @param string     $head
     *
     * @return DependencyCollectionDiff[]
     */
    public function scan(Repository $repository, string $base, string $head): array
    {
        $diffs = [];
        $paths = $this->git->fetchChangedFiles($repository, $base, $head);

        foreach ($paths as $path) {
            $absolutePath = $repository->getPath()->join($path);
            $scanner = $this->scannerResolver->resolve($absolutePath);

            if ($scanner === null) {
                continue;
            }

            $exists = $this->git->checkFileExists($repository, $base, $path);
            $pair = $scanner->scan(
                new FileDiff(
                    $exists ? $this->git->fetchOriginalFile($repository, $base, $path) : null,
                    file_get_contents($absolutePath->string()) ?: null,
                ),
            );

            $diffs[$path->string()] = $this->comparator->compare(
                $pair->getBefore(),
                $pair->getAfter(),
            );
        }

        return $diffs;
    }
}
