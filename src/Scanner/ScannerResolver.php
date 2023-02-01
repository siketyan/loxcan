<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner;

use Eloquent\Pathogen\PathInterface;

class ScannerResolver
{
    /**
     * @param ScannerInterface[] $scanners
     */
    public function __construct(
        private array $scanners,
    ) {
    }

    public function resolve(PathInterface $path): ?ScannerInterface
    {
        foreach ($this->scanners as $scanner) {
            if ($scanner->supports($path)) {
                return $scanner;
            }
        }

        return null;
    }
}
