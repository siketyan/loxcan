<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Cargo;

use Eloquent\Pathogen\PathInterface;
use Siketyan\Loxcan\Model\DependencyCollectionPair;
use Siketyan\Loxcan\Model\FileDiff;
use Siketyan\Loxcan\Scanner\ScannerInterface;

class CargoScanner implements ScannerInterface
{
    public function __construct(
        private readonly CargoLockParser $parser,
    ) {
    }

    public function scan(FileDiff $diff): DependencyCollectionPair
    {
        return new DependencyCollectionPair(
            $this->parser->parse($diff->getBefore()),
            $this->parser->parse($diff->getAfter()),
        );
    }

    public function supports(PathInterface $path): bool
    {
        return $path->name() === 'Cargo.lock';
    }
}
