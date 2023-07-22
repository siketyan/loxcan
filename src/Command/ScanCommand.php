<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Command;

use Eloquent\Pathogen\Path;
use Siketyan\Loxcan\Model\Repository;
use Siketyan\Loxcan\Reporter\Console\ConsoleReporter;
use Siketyan\Loxcan\UseCase\ReportUseCase;
use Siketyan\Loxcan\UseCase\ScanUseCase;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('scan')]
class ScanCommand extends Command
{
    public function __construct(
        private readonly ScanUseCase $useCase,
        private readonly ReportUseCase $reportUseCase,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('base', InputArgument::OPTIONAL)
            ->addArgument('head', InputArgument::OPTIONAL)
            ->addOption(
                'reporter',
                'r',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Reporter names to use for exporting diffs found.',
                ['console'],
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $repository = new Repository(Path::fromString(getcwd() ?: '.'));

        /** @var null|string $base */
        $base = $input->getArgument('base');
        /** @var null|string $head */
        $head = $input->getArgument('head');
        /** @var list<string> $reporters */
        $reporters = $input->getOption('reporter');

        $diffs = $this->useCase->scan($repository, $base, $head);

        $this->reportUseCase->report($diffs, $reporters, [
            ConsoleReporter::CONTEXT_SYMFONY_IO => $io,
        ]);

        return 0;
    }
}
