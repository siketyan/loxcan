<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Composer;

use Siketyan\Loxcan\Model\DependencyCollectionPair;
use Siketyan\Loxcan\Model\FileDiff;
use Siketyan\Loxcan\Scanner\ScannerInterface;

class ComposerScanner implements ScannerInterface
{
    private ComposerLockParser $parser;

    public function __construct(
        ComposerLockParser $parser
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

    public function supports(string $filename): bool
    {
        return $filename === 'composer.lock';
    }
}
