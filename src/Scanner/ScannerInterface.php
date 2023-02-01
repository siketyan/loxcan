<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner;

use Eloquent\Pathogen\PathInterface;
use Siketyan\Loxcan\Model\DependencyCollectionPair;
use Siketyan\Loxcan\Model\FileDiff;

interface ScannerInterface
{
    public function scan(FileDiff $diff): DependencyCollectionPair;

    public function supports(PathInterface $path): bool;
}
