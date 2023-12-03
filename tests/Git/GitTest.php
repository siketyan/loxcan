<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Git;

use Eloquent\Pathogen\Path;
use Eloquent\Pathogen\RelativePathInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Model\Repository;
use Symfony\Component\Process\Process;

class GitTest extends TestCase
{
    private GitProcessFactory&MockObject $processFactory;
    private Git $git;

    protected function setUp(): void
    {
        $this->processFactory = $this->createMock(GitProcessFactory::class);
        $this->git = new Git(
            $this->processFactory,
        );
    }

    public function testFetchChangedFiles(): void
    {
        $base = 'master';
        $head = 'feature';
        $repository = $this->createStub(Repository::class);

        $process = $this->createMock(Process::class);
        $process->expects($this->once())->method('run')->willReturn(0);
        $process->method('isSuccessful')->willReturn(true);
        $process->method('getOutput')->willReturn(<<<'EOS'
            foo/bar.json
            baz.lock
            EOS);

        $this->processFactory
            ->expects($this->once())
            ->method('create')
            ->with($repository, ['diff', '--name-only', 'master', 'feature'])
            ->willReturn($process)
        ;

        $files = $this->git->fetchChangedFiles($repository, $base, $head);

        $this->assertContainsOnlyInstancesOf(RelativePathInterface::class, $files);
        $this->assertSame('foo/bar.json', $files[0]->string());
        $this->assertSame('baz.lock', $files[1]->string());
    }

    public function testFetchOriginalFile(): void
    {
        $repository = $this->createMock(Repository::class);
        $expected = <<<'EOS'
            dummy
            foobar
            EOS;

        $process = $this->createMock(Process::class);
        $process->expects($this->once())->method('run')->willReturn(0);
        $process->method('isSuccessful')->willReturn(true);
        $process->method('getOutput')->willReturn($expected);

        $this->processFactory
            ->expects($this->once())
            ->method('create')
            ->with($repository, ['show', 'master:bar.lock'])
            ->willReturn($process)
        ;

        $path = $this->createStub(RelativePathInterface::class);
        $path->method('string')->willReturn('bar.lock');

        $actual = $this->git->fetchOriginalFile($repository, 'master', $path);

        $this->assertSame($expected, $actual);
    }

    public function testCheckFileExists(): void
    {
        $repository = $this->createStub(Repository::class);

        $process = $this->createMock(Process::class);
        $process->expects($this->exactly(2))->method('run')->willReturn(0);
        $process->method('isSuccessful')->willReturnOnConsecutiveCalls(true, false);

        $this->processFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->with($repository, ['cat-file', '-e', 'master:bar.lock'])
            ->willReturn($process)
        ;

        $path = $this->createStub(RelativePathInterface::class);
        $path->method('string')->willReturn('bar.lock');

        $this->assertTrue(
            $this->git->checkFileExists($repository, 'master', $path),
        );

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
