<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Git;

use Eloquent\Pathogen\Exception\NonRelativePathException;
use Eloquent\Pathogen\RelativePath;
use Eloquent\Pathogen\RelativePathInterface;
use Siketyan\Loxcan\Model\Repository;

class Git
{
    public function __construct(
        private readonly GitProcessFactory $processFactory,
    ) {
    }

    /**
     * @return list<RelativePathInterface>
     */
    public function fetchChangedFiles(Repository $repository, ?string $base = null, ?string $head = null): array
    {
        $process = $this->processFactory->create(
            $repository,
            array_filter(
                [
                    'diff',
                    '--name-only',
                    $base,
                    $head,
                ],
                fn (?string $v): bool => $v !== null,
            ),
        );

        $process->run();

        if (!$process->isSuccessful()) {
            throw new GitException(
                $process->getErrorOutput(),
            );
        }

        try {
            return array_map(
                fn (string $path): RelativePathInterface => RelativePath::fromString($path),
                array_filter(
                    explode("\n", $process->getOutput()),
                    fn (string $line): bool => $line !== '',
                ),
            );
        } catch (NonRelativePathException $e) {
            throw new GitException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }
    }

    public function fetchOriginalFile(Repository $repository, ?string $branch, RelativePathInterface $path): string
    {
        $process = $this->processFactory->create(
            $repository,
            [
                'show',
                sprintf('%s:%s', $branch ?? '', $path->string()),
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

    public function checkFileExists(Repository $repository, ?string $branch, RelativePathInterface $path): bool
    {
        $process = $this->processFactory->create(
            $repository,
            [
                'cat-file',
                '-e',
                sprintf('%s:%s', $branch ?? '', $path->string()),
            ],
        );

        $process->run();

        return $process->isSuccessful();
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
