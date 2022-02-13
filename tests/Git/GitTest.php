<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Git;

use Eloquent\Pathogen\Path;
use Eloquent\Pathogen\RelativePathInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Siketyan\Loxcan\Model\Repository;
use Symfony\Component\Process\Process;

class GitTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $processFactory;
    private Git $git;

    protected function setUp(): void
    {
        $this->processFactory = $this->prophesize(GitProcessFactory::class);

        $this->git = new Git(
            $this->processFactory->reveal(),
        );
    }

    public function testFetchChangedFiles(): void
    {
        $base = 'master';
        $head = 'feature';
        $repository = $this->prophesize(Repository::class)->reveal();

        $process = $this->prophesize(Process::class);
        $process->run()->willReturn(0)->shouldBeCalledOnce();
        $process->isSuccessful()->willReturn(true);
        $process->getOutput()->willReturn(<<<'EOS'
            foo/bar.json
            baz.lock
            EOS);

        $this->processFactory
            ->create($repository, ['diff', '--name-only', 'master', 'feature'])
            ->willReturn($process->reveal())
            ->shouldBeCalledOnce()
        ;

        $files = $this->git->fetchChangedFiles($repository, $base, $head);

        $this->assertContainsOnlyInstancesOf(RelativePathInterface::class, $files);
        $this->assertSame('foo/bar.json', $files[0]->string());
        $this->assertSame('baz.lock', $files[1]->string());
    }

    public function testFetchOriginalFile(): void
    {
        $repository = $this->prophesize(Repository::class)->reveal();
        $expected = <<<'EOS'
            dummy
            foobar
            EOS;

        $process = $this->prophesize(Process::class);
        $process->run()->willReturn(0)->shouldBeCalledOnce();
        $process->isSuccessful()->willReturn(true);
        $process->getOutput()->willReturn($expected);

        $this->processFactory
            ->create($repository, ['show', 'master:bar.lock'])
            ->willReturn($process->reveal())
            ->shouldBeCalledOnce()
        ;

        $path = $this->prophesize(RelativePathInterface::class);
        $path->string()->willReturn('bar.lock');
        $path = $path->reveal();

        $actual = $this->git->fetchOriginalFile($repository, 'master', $path);

        $this->assertSame($expected, $actual);
    }

    public function testCheckFileExists(): void
    {
        $repository = $this->prophesize(Repository::class)->reveal();

        $process = $this->prophesize(Process::class);
        $process->run()->willReturn(0)->shouldBeCalledTimes(2);
        $process->isSuccessful()->willReturn(true);

        $this->processFactory
            ->create($repository, ['cat-file', '-e', 'master:bar.lock'])
            ->willReturn($process->reveal())
            ->shouldBeCalledTimes(2)
        ;

        $path = $this->prophesize(RelativePathInterface::class);
        $path->string()->willReturn('bar.lock');
        $path = $path->reveal();

        $this->assertTrue(
            $this->git->checkFileExists($repository, 'master', $path),
        );

        $process->isSuccessful()->willReturn(false);

        $this->assertFalse(
            $this->git->checkFileExists($repository, 'master', $path),
        );
    }

    public function testSupports(): void
    {
        $this->assertTrue(
            $this->git->supports(
                new Repository(
                    Path::fromString(__DIR__ . '/../..'),
                ),
            ),
        );
    }
}
