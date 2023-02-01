<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\UseCase;

use Siketyan\Loxcan\Comparator\DependencyCollectionComparator;
use Siketyan\Loxcan\Git\Git;
use Siketyan\Loxcan\Model\DependencyCollectionDiff;
use Siketyan\Loxcan\Model\FileDiff;
use Siketyan\Loxcan\Model\Repository;
use Siketyan\Loxcan\Scanner\ScannerInterface;
use Siketyan\Loxcan\Scanner\ScannerResolver;

class ScanUseCase
{
    public function __construct(
        private readonly Git $git,
        private readonly ScannerResolver $scannerResolver,
        private readonly DependencyCollectionComparator $comparator,
    ) {
    }

    /**
     * @return DependencyCollectionDiff[]
     */
    public function scan(Repository $repository, ?string $base = null, ?string $head = null): array
    {
        $diffs = [];
        $paths = $this->git->fetchChangedFiles($repository, $base, $head);

        foreach ($paths as $path) {
            $absolutePath = $repository->getPath()->join($path);
            $scanner = $this->scannerResolver->resolve($absolutePath);

            if (!$scanner instanceof ScannerInterface) {
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
