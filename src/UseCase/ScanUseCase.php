<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\UseCase;

use Eloquent\Pathogen\Exception\NonRelativePathException;
use Eloquent\Pathogen\RelativePath;
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
     *
     * @throws NonRelativePathException
     */
    public function scan(Repository $repository, string $base, string $head): array
    {
        $diffs = [];
        $files = $this->git->fetchChangedFiles($repository, $base, $head);

        foreach ($files as $file) {
            $scanner = $this->scannerResolver->resolve(
                $repository->getPath()->join(
                    RelativePath::fromString($file),
                ),
            );

            if ($scanner === null) {
                continue;
            }

            $pair = $scanner->scan(
                new FileDiff(
                    $this->git->fetchOriginalFile($repository, $base, $file),
                    file_get_contents($file),
                ),
            );

            $diffs[$file] = $this->comparator->compare(
                $pair->getBefore(),
                $pair->getAfter(),
            );
        }

        return $diffs;
    }
}
