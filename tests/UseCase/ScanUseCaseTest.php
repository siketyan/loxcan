<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\UseCase;

use Eloquent\Pathogen\PathInterface;
use Eloquent\Pathogen\RelativePathInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Comparator\DependencyCollectionComparator;
use Siketyan\Loxcan\Git\Git;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\DependencyCollectionDiff;
use Siketyan\Loxcan\Model\DependencyCollectionPair;
use Siketyan\Loxcan\Model\FileDiff;
use Siketyan\Loxcan\Model\Repository;
use Siketyan\Loxcan\Scanner\ScannerInterface;
use Siketyan\Loxcan\Scanner\ScannerResolver;

class ScanUseCaseTest extends TestCase
{
    private Git&MockObject $git;
    private MockObject&ScannerResolver $scannerResolver;
    private DependencyCollectionComparator&MockObject $comparator;
    private ScanUseCase $useCase;

    protected function setUp(): void
    {
        $this->git = $this->createMock(Git::class);
        $this->scannerResolver = $this->createMock(ScannerResolver::class);
        $this->comparator = $this->createMock(DependencyCollectionComparator::class);

        $this->useCase = new ScanUseCase(
            $this->git,
            $this->scannerResolver,
            $this->comparator,
        );
    }

    public function test(): void
    {
        $makeRelativePath = function (string $path): RelativePathInterface {
            $stub = $this->createStub(RelativePathInterface::class);
            $stub->method('string')->willReturn($path);

            return $stub;
        };

        $base = 'main';
        $head = 'feature';
        $files = [
            $makeRelativePath('./file1.php'),
            $makeRelativePath('./file2.php'),
        ];

        $file0Path = $this->createStub(PathInterface::class);
        $file0Path->method('string')->willReturn(__FILE__);

        $file1Path = $this->createStub(PathInterface::class);

        $repositoryPath = $this->createStub(PathInterface::class);
        $repositoryPath->method('join')->willReturnMap([
            [$files[0], $file0Path],
            [$files[1], $file1Path],
        ]);

        $repository = $this->createStub(Repository::class);
        $repository->method('getPath')->willReturn($repositoryPath);

        $before = $this->createStub(DependencyCollection::class);
        $after = $this->createStub(DependencyCollection::class);
        $diff = $this->createStub(DependencyCollectionDiff::class);

        $pair = $this->createStub(DependencyCollectionPair::class);
        $pair->method('getBefore')->willReturn($before);
        $pair->method('getAfter')->willReturn($after);

        $scanner = $this->createMock(ScannerInterface::class);

        $scanner
            ->expects($this->once())
            ->method('scan')
            ->with($this->callback(static fn (FileDiff $d): bool => $d->getBefore() === 'foo'))
            ->willReturn($pair)
        ;

        $this->git->expects($this->once())->method('fetchChangedFiles')->with($repository, $base, $head)->willReturn($files);
        $this->git->expects($this->once())->method('fetchOriginalFile')->with($repository, $base, $files[0])->willReturn('foo');
        $this->git->expects($this->once())->method('checkFileExists')->with($repository, $base, $files[0])->willReturn(true);

        $this->scannerResolver
            ->method('resolve')
            ->willReturnCallback(static fn (PathInterface $p): ?ScannerInterface => match ($p) {
                $file0Path => $scanner,
                default => null,
            })
        ;

        $this->comparator->method('compare')->with($before, $after)->willReturn($diff);

        $diffs = $this->useCase->scan($repository, $base, $head);

        $this->assertCount(1, $diffs);
        $this->assertSame($diff, $diffs[array_key_first($diffs)]);
    }
}
