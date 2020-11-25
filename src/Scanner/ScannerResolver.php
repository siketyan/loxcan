<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner;

use Eloquent\Pathogen\PathInterface;

class ScannerResolver
{
    /**
     * @var ScannerInterface[]
     */
    private array $scanners;

    /**
     * @param ScannerInterface[] $scanners
     */
    public function __construct(
        array $scanners
    ) {
        $this->scanners = $scanners;
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
