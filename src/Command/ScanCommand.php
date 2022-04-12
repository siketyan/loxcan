<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Command;

use Eloquent\Pathogen\Path;
use JetBrains\PhpStorm\Pure;
use Siketyan\Loxcan\Model\DependencyCollectionDiff;
use Siketyan\Loxcan\Model\Repository;
use Siketyan\Loxcan\Versioning\VersionDiff;
use Siketyan\Loxcan\UseCase\ReportUseCase;
use Siketyan\Loxcan\UseCase\ScanUseCase;
use Symfony\Component\Console\Color;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ScanCommand extends Command
{
    private const NAME = 'scan';

    public function __construct(
        private ScanUseCase $useCase,
        private ReportUseCase $reportUseCase
    ) {
        parent::__construct(self::NAME);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('base', InputArgument::OPTIONAL)
            ->addArgument('head', InputArgument::OPTIONAL)
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $repository = new Repository(Path::fromString(getcwd()));

        /** @var string|null $base */
        $base = $input->getArgument('base');
        /** @var string|null $head */
        $head = $input->getArgument('head');

        $diffs = $this->useCase->scan($repository, $base, $head);

        if (count($diffs) === 0) {
            $io->writeln(
                '✨ No lock file changes found, looks shine!',
            );
        } else {
            $this->printDiffs($io, $diffs);
        }

        $this->reportUseCase->report($diffs);

        return 0;
    }

    /**
     * @param SymfonyStyle               $io
     * @param DependencyCollectionDiff[] $diffs
     */
    private function printDiffs(SymfonyStyle $io, array $diffs): void
    {
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
}
