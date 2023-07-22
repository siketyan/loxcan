<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Command;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Siketyan\Loxcan\Model\DependencyCollectionDiff;
use Siketyan\Loxcan\Model\Repository;
use Siketyan\Loxcan\UseCase\ReportUseCase;
use Siketyan\Loxcan\UseCase\ScanUseCase;
use Symfony\Component\Console\Tester\CommandTester;

class ScanCommandTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<ScanUseCase>
     */
    private ObjectProphecy $scanUseCase;

    /**
     * @var ObjectProphecy<ReportUseCase>
     */
    private ObjectProphecy $reportUseCase;

    private CommandTester $tester;

    protected function setUp(): void
    {
        $this->scanUseCase = $this->prophesize(ScanUseCase::class);
        $this->reportUseCase = $this->prophesize(ReportUseCase::class);

        $this->tester = new CommandTester(
            new ScanCommand(
                $this->scanUseCase->reveal(),
                $this->reportUseCase->reveal(),
            ),
        );
    }

    public function test(): void
    {
        $diff = $this->prophesize(DependencyCollectionDiff::class);
        $diffs = [
            'foo.lock' => $diff->reveal(),
            'bar.lock' => $diff->reveal(),
        ];

        $this->scanUseCase
            ->scan(Argument::type(Repository::class), 'foo', 'bar')
            ->willReturn($diffs)
            ->shouldBeCalledOnce()
        ;

        $this->reportUseCase
            ->report($diffs, ['console'], Argument::type('array'))
            ->shouldBeCalledOnce()
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
