<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner;

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

    public function resolve(string $filename): ?ScannerInterface
    {
        foreach ($this->scanners as $scanner) {
            if ($scanner->supports($filename)) {
                return $scanner;
            }
        }

        return null;
    }
}
