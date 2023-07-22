<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

class DependencyCollectionDiff
{
    /**
     * @param list<Dependency>     $added
     * @param list<DependencyDiff> $updated
     * @param list<Dependency>     $removed
     */
    public function __construct(
        private readonly array $added = [],
        private readonly array $updated = [],
        private readonly array $removed = [],
    ) {
    }

    /**
     * @return list<Dependency>
     */
    public function getAdded(): array
    {
        return $this->added;
    }

    /**
     * @return list<DependencyDiff>
     */
    public function getUpdated(): array
    {
        return $this->updated;
    }

    /**
     * @return list<Dependency>
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
