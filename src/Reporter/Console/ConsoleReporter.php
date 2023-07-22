<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter\Console;

use JetBrains\PhpStorm\Pure;
use Siketyan\Loxcan\Reporter\ReporterInterface;
use Siketyan\Loxcan\Versioning\VersionDiff;
use Symfony\Component\Console\Color;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConsoleReporter implements ReporterInterface
{
    public const CONTEXT_SYMFONY_IO = 'console.symfony-style';

    public function report(array $diffs, array $context = []): void
    {
        $io = $context[self::CONTEXT_SYMFONY_IO] ?? null;
        \assert($io instanceof SymfonyStyle);

        if ($diffs === []) {
            $io->writeln(
                '✨ No lock file changes found, looks shine!',
            );

            return;
        }

        foreach ($diffs as $file => $diff) {
            $io->section($file);

            if ($diff->count() === 0) {
                $io->writeln(
                    '🔄 The file was updated, but no dependency changes found.',
                );

                continue;
            }

            $rows = [];

            foreach ($diff->getAdded() as $dependency) {
                $rows[] = [
                    '➕',
                    $dependency->getPackage()->getName(),
                    '',
                    $dependency->getVersion(),
                ];
            }

            foreach ($diff->getUpdated() as $dependencyDiff) {
                $versionDiff = $dependencyDiff->getVersionDiff();
                $rows[] = [
                    $this->getVersionDiffTypeEmoji($versionDiff),
                    $this->emphasizeBreakingChanges($versionDiff, $dependencyDiff->getPackage()->getName()),
                    $this->emphasizeBreakingChanges($versionDiff, (string) $versionDiff->getBefore()),
                    $this->emphasizeBreakingChanges($versionDiff, (string) $versionDiff->getAfter()),
                ];
            }

            foreach ($diff->getRemoved() as $dependency) {
                $rows[] = [
                    '➖',
                    $dependency->getPackage()->getName(),
                    $dependency->getVersion(),
                    '',
                ];
            }

            $io->table(
                ['', 'Package', 'Before', 'After'],
                $rows,
            );
        }
    }

    #[Pure]
    private function getVersionDiffTypeEmoji(VersionDiff $diff): string
    {
        switch ($diff->getType()) {
            case VersionDiff::UPGRADED:
                return '⬆️';

            case VersionDiff::DOWNGRADED:
                return '⬇️';

            case VersionDiff::CHANGED:
                return '💥';

            default:
            case VersionDiff::UNKNOWN:
                return '🔄';
        }
    }

    private function emphasizeBreakingChanges(VersionDiff $diff, string $str): string
    {
        $emphasize = new Color('bright-white', '', ['bold']);

        if (!$diff->isCompatible()) {
            return $emphasize->apply($str);
        }

        return $str;
    }

    public function supports(): bool
    {
        return true;
    }

    public function name(): string
    {
        return 'console';
    }
}
