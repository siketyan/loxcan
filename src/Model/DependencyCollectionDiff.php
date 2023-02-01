<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

class DependencyCollectionDiff
{
    /**
     * @param Dependency[]     $added
     * @param DependencyDiff[] $updated
     * @param Dependency[]     $removed
     */
    public function __construct(
        private array $added = [],
        private array $updated = [],
        private array $removed = [],
    ) {
    }

    /**
     * @return Dependency[]
     */
    public function getAdded(): array
    {
        return $this->added;
    }

    /**
     * @return DependencyDiff[]
     */
    public function getUpdated(): array
    {
        return $this->updated;
    }

    /**
     * @return Dependency[]
     */
    public function getRemoved(): array
    {
        return $this->removed;
    }

    public function count(): int
    {
        return \count($this->added) + \count($this->updated) + \count($this->removed);
    }
}
