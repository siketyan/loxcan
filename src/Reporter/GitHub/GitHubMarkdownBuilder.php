<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter\GitHub;

use Siketyan\Loxcan\Model\DependencyCollectionDiff;
use Siketyan\Loxcan\Versioning\VersionDiff;

class GitHubMarkdownBuilder
{
    public function build(DependencyCollectionDiff $diff, string $filename): string
    {
        return implode("\n", [
            sprintf('#### %s', $filename),
            ...$this->buildTable($diff),
        ]);
    }

    /**
     * @param DependencyCollectionDiff $diff
     *
     * @return string[]
     */
    private function buildTable(DependencyCollectionDiff $diff): array
    {
        $rows = [
            '||Package|Before|After|',
            '|---|---|---|---|',
        ];

        foreach ($diff->getAdded() as $dependency) {
            $rows[] = sprintf(
                '|➕|%s||%s|',
                $dependency->getPackage()->getName(),
                $dependency->getVersion(),
            );
        }

        foreach ($diff->getUpdated() as $dependencyDiff) {
            $versionDiff = $dependencyDiff->getVersionDiff();
            $rows[] = sprintf(
                '|%s|%s|%s|%s|',
                $versionDiff->getType() === VersionDiff::UPGRADED ? '⬆️' : '⬇️',
                $dependencyDiff->getPackage()->getName(),
                $versionDiff->getBefore(),
                $versionDiff->getAfter(),
            );
        }

        foreach ($diff->getRemoved() as $dependency) {
            $rows[] = sprintf(
                '|➖|%s|%s||',
                $dependency->getPackage()->getName(),
                $dependency->getVersion(),
            );
        }

        return $rows;
    }
}
