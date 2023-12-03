<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Model\DependencyCollectionDiff;
use Siketyan\Loxcan\Model\Repository;
use Siketyan\Loxcan\UseCase\ReportUseCase;
use Siketyan\Loxcan\UseCase\ScanUseCase;
use Symfony\Component\Console\Tester\CommandTester;

class ScanCommandTest extends TestCase
{
    private MockObject&ScanUseCase $scanUseCase;
    private MockObject&ReportUseCase $reportUseCase;
    private CommandTester $tester;

    protected function setUp(): void
    {
        $this->scanUseCase = $this->createMock(ScanUseCase::class);
        $this->reportUseCase = $this->createMock(ReportUseCase::class);

        $this->tester = new CommandTester(
            new ScanCommand(
                $this->scanUseCase,
                $this->reportUseCase,
            ),
        );
    }

    public function test(): void
    {
        $diff = $this->createStub(DependencyCollectionDiff::class);
        $diffs = [
            'foo.lock' => $diff,
            'bar.lock' => $diff,
        ];

        $this->scanUseCase
            ->expects($this->once())
            ->method('scan')
            ->with($this->isInstanceOf(Repository::class), 'foo', 'bar')
            ->willReturn($diffs)
        ;

        $this->reportUseCase
            ->expects($this->once())
            ->method('report')
            ->with($diffs, ['console'], $this->isType('array'))
        ;

        $exitCode = $this->tester->execute([
            'base' => 'foo',
            'head' => 'bar',
        ], [
            'reporter' => ['console'],
        ]);

        $this->assertSame(0, $exitCode);
    }
}
