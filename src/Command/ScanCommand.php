<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Command;

use Eloquent\Pathogen\Path;
use Siketyan\Loxcan\Model\Repository;
use Siketyan\Loxcan\Model\VersionDiff;
use Siketyan\Loxcan\UseCase\ScanUseCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ScanCommand extends Command
{
    private const NAME = 'scan';

    private ScanUseCase $useCase;

    public function __construct(ScanUseCase $useCase)
    {
        parent::__construct(self::NAME);

        $this->useCase = $useCase;
    }

    protected function configure()
    {
        $this
            ->addArgument('base', InputArgument::REQUIRED)
            ->addArgument('head', InputArgument::REQUIRED)
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $repository = new Repository(Path::fromString(getcwd()));
        $base = (string) $input->getArgument('base');
        $head = (string) $input->getArgument('head');

        $diffs = $this->useCase->scan($repository, $base, $head);

        foreach ($diffs as $file => $diff) {
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
                    $versionDiff->getType() === VersionDiff::UPGRADED ? '⬆️' : '⬇️',
                    $dependencyDiff->getPackage()->getName(),
                    $versionDiff->getBefore(),
                    $versionDiff->getAfter(),
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

            $io->section($file);
            $io->table(
                ['', 'Package', 'Before', 'After'],
                $rows,
            );
        }

        return 0;
    }
}
