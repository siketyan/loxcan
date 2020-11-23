<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

class DependencyCollection
{
    /**
     * @var Dependency[]
     */
    private array $dependencies;

    /**
     * @param Dependency[] $dependencies
     */
    public function __construct(
        array $dependencies
    ) {
        $this->dependencies = $dependencies;
    }

    /**
     * @return Dependency[]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }
}
