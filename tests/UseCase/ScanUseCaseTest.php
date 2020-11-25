<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\UseCase;

use Eloquent\Pathogen\Exception\NonRelativePathException;
use Eloquent\Pathogen\PathInterface;
use Eloquent\Pathogen\RelativePathInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
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
    use ProphecyTrait;

    private ObjectProphecy $git;
    private ObjectProphecy $scannerResolver;
    private ObjectProphecy $comparator;
    private ScanUseCase $useCase;

    protected function setUp(): void
    {
        $this->git = $this->prophesize(Git::class);
        $this->scannerResolver = $this->prophesize(ScannerResolver::class);
        $this->comparator = $this->prophesize(DependencyCollectionComparator::class);

        $this->useCase = new ScanUseCase(
            $this->git->reveal(),
            $this->scannerResolver->reveal(),
            $this->comparator->reveal(),
        );
    }

    /**
     * @throws NonRelativePathException
     */
    public function test(): void
    {
        $base = 'main';
        $head = 'feature';
        $files = [
            'composer.lock',
            'unsupported.lock',
        ];

        $file0Path = $this->prophesize(PathInterface::class)->reveal();
        $file1Path = $this->prophesize(PathInterface::class)->reveal();

        $repositoryPath = $this->prophesize(PathInterface::class);

        /** @noinspection PhpParamsInspection */
        $repositoryPath
            ->join(Argument::that(fn (RelativePathInterface $p): bool => $p->name() === $files[0]))
            ->willReturn($file0Path)
        ;

        /** @noinspection PhpParamsInspection */
        $repositoryPath
            ->join(Argument::that(fn (RelativePathInterface $p): bool => $p->name() === $files[1]))
            ->willReturn($file1Path)
        ;

        $repository = $this->prophesize(Repository::class);
        $repository->getPath()->willReturn($repositoryPath->reveal());
        $repository = $repository->reveal();

        $before = $this->prophesize(DependencyCollection::class)->reveal();
        $after = $this->prophesize(DependencyCollection::class)->reveal();
        $diff = $this->prophesize(DependencyCollectionDiff::class)->reveal();

        $pair = $this->prophesize(DependencyCollectionPair::class);
        $pair->getBefore()->willReturn($before);
        $pair->getAfter()->willReturn($after);

        $scanner = $this->prophesize(ScannerInterface::class);

        /** @noinspection PhpParamsInspection */
        $scanner
            ->scan(Argument::that(fn (FileDiff $d): bool => $d->getBefore() === 'foo'))
            ->willReturn($pair)
            ->shouldBeCalledOnce()
        ;

        $this->git->fetchChangedFiles($repository, $base, $head)->willReturn($files)->shouldBeCalledOnce();
        $this->git->fetchOriginalFile($repository, $base, $files[0])->willReturn('foo')->shouldBeCalledOnce();
        $this->git->fetchOriginalFile($repository, $base, $files[1])->shouldNotBeCalled();
        $this->git->checkFileExists($repository, $base, $files[0])->willReturn(true)->shouldBeCalledOnce();

        $this->scannerResolver->resolve($file0Path)->willReturn($scanner->reveal());
        $this->scannerResolver->resolve($file1Path)->willReturn(null);

        $this->comparator->compare($before, $after)->willReturn($diff);

        $diffs = $this->useCase->scan($repository, $base, $head);

        $this->assertCount(1, $diffs);
        $this->assertSame($diff, $diffs[array_key_first($diffs)]);
    }
}
