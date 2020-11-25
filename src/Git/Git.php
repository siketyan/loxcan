<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Git;

use Siketyan\Loxcan\Model\Repository;

class Git
{
    private GitProcessFactory $processFactory;

    public function __construct(
        GitProcessFactory $processFactory
    ) {
        $this->processFactory = $processFactory;
    }

    /**
     * @param Repository $repository
     * @param string     $base
     * @param string     $head
     *
     * @return string[]
     */
    public function fetchChangedFiles(Repository $repository, string $base, string $head = ''): array
    {
        $process = $this->processFactory->create(
            $repository,
            [
                'diff',
                '--name-only',
                sprintf('%s..%s', $base, $head),
            ],
        );

        $process->run();

        if (!$process->isSuccessful()) {
            throw new GitException(
                $process->getErrorOutput(),
            );
        }

        return array_filter(
            explode(PHP_EOL, $process->getOutput()),
            fn (string $line): bool => $line !== '',
        );
    }

    public function fetchOriginalFile(Repository $repository, string $branch, string $path): string
    {
        $process = $this->processFactory->create(
            $repository,
            [
                'show',
                sprintf('%s:%s', $branch, $path),
            ],
        );

        $process->run();

        if (!$process->isSuccessful()) {
            throw new GitException(
                $process->getErrorOutput(),
            );
        }

        return $process->getOutput();
    }

    public function checkFileExists(Repository $repository, string $branch, string $path): bool
    {
        $process = $this->processFactory->create(
            $repository,
            [
                'cat-file',
                '-e',
                sprintf('%s:%s', $branch, $path),
            ],
        );

        $process->run();

        if (!$process->isSuccessful()) {
            return false;
        }

        return true;
    }

    public function supports(Repository $repository): bool
    {
        return is_dir(
            $repository
                ->getPath()
                ->joinAtomSequence(['.git'])
                ->string(),
        );
    }
}
