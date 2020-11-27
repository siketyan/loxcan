<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter\GitHub;

use Siketyan\Loxcan\Model\DependencyCollectionDiff;
use Siketyan\Loxcan\Versioning\VersionDiff;

class GitHubMarkdownBuilder
{
    public function build(array $diffs): string
    {
        if (count($diffs) === 0) {
            return '✨ No lock file changes found, looks shine!';
        }

        $sections = [];

        foreach ($diffs as $filename => $diff) {
            $sections[] = $this->buildSection($diff, $filename);
        }

        return implode("\n\n", $sections);
    }

    public function buildSection(DependencyCollectionDiff $diff, string $filename): string
    {
        $rows = [
            sprintf('#### %s', $filename),
        ];

        if ($diff->count() === 0) {
            $rows[] = '🔄 The file was updated, but no dependency changes found.';
        } else {
            $rows = array_merge(
                $rows,
                $this->buildTable($diff),
            );
        }

        return implode("\n", $rows);
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
                $this->getVersionDiffTypeEmoji($versionDiff),
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

    private function getVersionDiffTypeEmoji(VersionDiff $diff): string
    {
        switch ($diff->getType()) {
            case VersionDiff::UPGRADED:
                return '⬆️';

            case VersionDiff::DOWNGRADED:
                return '⬇️';

            default:
            case VersionDiff::UNKNOWN:
                return '🔄';
        }
    }
}
