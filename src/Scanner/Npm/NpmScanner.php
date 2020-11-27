<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Npm;

use Eloquent\Pathogen\PathInterface;
use Siketyan\Loxcan\Model\DependencyCollectionPair;
use Siketyan\Loxcan\Model\FileDiff;
use Siketyan\Loxcan\Scanner\ScannerInterface;

class NpmScanner implements ScannerInterface
{
    private NpmLockParser $parser;

    public function __construct(
        NpmLockParser $parser
    ) {
        $this->parser = $parser;
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
        return $path->name() === 'package-lock.json';
    }
}
