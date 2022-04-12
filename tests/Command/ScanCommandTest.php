<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Command;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollectionDiff;
use Siketyan\Loxcan\Model\DependencyDiff;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Model\Repository;
use Siketyan\Loxcan\UseCase\ReportUseCase;
use Siketyan\Loxcan\UseCase\ScanUseCase;
use Siketyan\Loxcan\Versioning\Simple\SimpleVersion;
use Siketyan\Loxcan\Versioning\VersionDiff;
use Symfony\Component\Console\Tester\CommandTester;

class ScanCommandTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $scanUseCase;
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
        $diff->count()->willReturn(5);
        $diff->getAdded()->willReturn([$this->createDependency('added', 'v1.2.3')]);
        $diff->getRemoved()->willReturn([$this->createDependency('removed', 'v3.2.1')]);
        $diff->getUpdated()->willReturn([
            $this->createDependencyDiff('upgraded', 'v1.1.1', 'v2.2.2', VersionDiff::UPGRADED),
            $this->createDependencyDiff('downgraded', 'v4.4.4', 'v3.3.3', VersionDiff::DOWNGRADED),
            $this->createDependencyDiff('unknown', 'v5.5.5', 'v5.5.5', VersionDiff::UNKNOWN),
        ]);

        $emptyDiff = $this->prophesize(DependencyCollectionDiff::class);
        $emptyDiff->count()->willReturn(0);

        $diffs = [
            'foo.lock' => $diff->reveal(),
            'bar.lock' => $emptyDiff->reveal(),
        ];

        $this->scanUseCase
            ->scan(Argument::type(Repository::class), 'foo', 'bar')
            ->willReturn($diffs)
            ->shouldBeCalledOnce()
        ;

        $this->reportUseCase
            ->report($diffs)
            ->shouldBeCalledOnce()
        ;

        $exitCode = $this->tester->execute([
            'base' => 'foo',
            'head' => 'bar',
        ]);

        $this->assertSame(0, $exitCode);

        // In PHP 8.1.0, the problem is fixed that some emojis are counted as 2 chars.
        // So we need to switch the expectation in this test.
        if (version_compare(PHP_VERSION, '8.1.0', '>=')) {
            $this->assertSame(
                <<<'EOS'

                    foo.lock
                    --------

                     ---- ------------ -------- -------- 
                           Package      Before   After   
                     ---- ------------ -------- -------- 
                      ➕   added                 v1.2.3  
                      ⬆️   upgraded     v1.1.1   v2.2.2  
                      ⬇️   downgraded   v4.4.4   v3.3.3  
                      🔄   unknown      v5.5.5   v5.5.5  
                      ➖   removed      v3.2.1           
                     ---- ------------ -------- -------- 

                    bar.lock
                    --------

                    🔄 The file was updated, but no dependency changes found.

                    EOS,
                $this->removeTextStyles($this->tester->getDisplay()),
            );
        } else {
            $this->assertSame(
                <<<'EOS'

                    foo.lock
                    --------

                     ---- ------------ -------- -------- 
                           Package      Before   After   
                     ---- ------------ -------- -------- 
                      ➕    added                 v1.2.3  
                      ⬆️   upgraded     v1.1.1   v2.2.2  
                      ⬇️   downgraded   v4.4.4   v3.3.3  
                      🔄    unknown      v5.5.5   v5.5.5  
                      ➖    removed      v3.2.1           
                     ---- ------------ -------- -------- 

                    bar.lock
                    --------

                    🔄 The file was updated, but no dependency changes found.

                    EOS,
                $this->removeTextStyles($this->tester->getDisplay()),
            );
        }
    }

    public function testNoDiff(): void
    {
        $this->scanUseCase
            ->scan(Argument::type(Repository::class), 'foo', 'bar')
            ->willReturn([])
            ->shouldBeCalledOnce()
        ;

        $this->reportUseCase
            ->report([])
            ->shouldBeCalledOnce()
        ;

        $exitCode = $this->tester->execute([
           'base' => 'foo',
           'head' => 'bar',
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertSame(
            <<<EOS
                ✨ No lock file changes found, looks shine!

                EOS,
            $this->tester->getDisplay(),
        );
    }

    private function createDependency(string $name, string $versionName): Dependency
    {
        $package = $this->prophesize(Package::class);
        $package->getName()->willReturn($name);

        $version = $this->prophesize(SimpleVersion::class);
        $version->__toString()->willReturn($versionName);

        $dependency = $this->prophesize(Dependency::class);
        $dependency->getPackage()->willReturn($package->reveal());
        $dependency->getVersion()->willReturn($version->reveal());

        return $dependency->reveal();
    }

    private function createDependencyDiff(string $name, string $before, string $after, int $type): DependencyDiff
    {
        $package = $this->prophesize(Package::class);
        $package->getName()->willReturn($name);

        $beforeVersion = $this->prophesize(SimpleVersion::class);
        $beforeVersion->__toString()->willReturn($before);

        $afterVersion = $this->prophesize(SimpleVersion::class);
        $afterVersion->__toString()->willReturn($after);

        $versionDiff = $this->prophesize(VersionDiff::class);
        $versionDiff->isCompatible()->willReturn(false);
        $versionDiff->getType()->willReturn($type);
        $versionDiff->getBefore()->willReturn($beforeVersion->reveal());
        $versionDiff->getAfter()->willReturn($afterVersion->reveal());

        $diff = $this->prophesize(DependencyDiff::class);
        $diff->getPackage()->willReturn($package->reveal());
        $diff->getVersionDiff()->willReturn($versionDiff->reveal());

        return $diff->reveal();
    }

    private function removeTextStyles(string $text): string
    {
        return preg_replace('/\e\[[0-9;]*m/', '', $text) ?? $text;
    }
}
