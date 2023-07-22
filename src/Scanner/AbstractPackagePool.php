<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner;

use Siketyan\Loxcan\Model\Package;

abstract class AbstractPackagePool
{
    /**
     * @param array<string, Package> $packages
     */
    public function __construct(
        private array $packages = [],
    ) {
    }

    public function get(string $name, ?string $constraint = null): ?Package
    {
        return $this->packages[$this->getKey($name, $constraint)] ?? null;
    }

    public function add(Package $package): void
    {
        $this->packages[$this->getKey($package->getName(), $package->getConstraint())] = $package;
    }

    private function getKey(string $name, ?string $constraint): string
    {
        $key = $name;
        if ($constraint !== null) {
            $key .= '__' . $constraint;
        }

        return $key;
    }
}
