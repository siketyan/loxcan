<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Git;

use Siketyan\Loxcan\Model\Repository;
use Symfony\Component\Process\Process;

class GitProcessFactory
{
    public function __construct(
        private readonly string $path = 'git',
    ) {
    }

    /**
     * @param list<string> $command
     */
    public function create(Repository $repository, array $command): Process
    {
        return new Process(
            [$this->path, ...$command],
            $repository->getPath()->string(),
        );
    }
}
