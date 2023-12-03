<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter\GitHub;

use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollectionDiff;
use Siketyan\Loxcan\Model\DependencyDiff;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Reporter\MarkdownBuilder;
use Siketyan\Loxcan\Versioning\Simple\SimpleVersion;
use Siketyan\Loxcan\Versioning\VersionDiff;

class GitHubMarkdownBuilderTest extends TestCase
{
    private MarkdownBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new MarkdownBuilder();
    }

    public function test(): void
    {
        $diff = $this->createStub(DependencyCollectionDiff::class);
        $diff->method('count')->willReturn(5);
        $diff->method('getAdded')->willReturn([$this->createDependency('added', 'v1.2.3')]);
        $diff->method('getRemoved')->willReturn([$this->createDependency('removed', 'v3.2.1')]);
        $diff->method('getUpdated')->willReturn([
            $this->createDependencyDiff('upgraded', 'v1.1.1', 'v2.2.2', VersionDiff::UPGRADED),
            $this->createDependencyDiff('downgraded', 'v4.4.4', 'v3.3.3', VersionDiff::DOWNGRADED),
            $this->createDependencyDiff('unknown', 'v5.5.5', 'v5.5.5', VersionDiff::UNKNOWN),
        ]);

        $emptyDiff = $this->createStub(DependencyCollectionDiff::class);
        $emptyDiff->method('count')->willReturn(0);

        $markdown = $this->builder->build([
            'foo.lock' => $diff,
            'bar.lock' => $emptyDiff,
        ]);

        $this->assertSame(
            <<<'EOS'
                #### foo.lock
                ||Package|Before|After|
                |---|---|---|---|
                |➕|added||v1.2.3|
                |⬆️|**upgraded**|**v1.1.1**|**v2.2.2**|
                |⬇️|**downgraded**|**v4.4.4**|**v3.3.3**|
                |🔄|**unknown**|**v5.5.5**|**v5.5.5**|
                |➖|removed|v3.2.1||

                #### bar.lock
                🔄 The file was updated, but no dependency changes found.
                EOS,
            $markdown,
        );
    }

    public function testNoDiff(): void
    {
        $markdown = $this->builder->build([]);

        $this->assertSame(
            '✨ No lock file changes found, looks shine!',
            $markdown,
        );
    }

    private function createDependency(string $name, string $versionName): Dependency
    {
        $package = $this->createStub(Package::class);
        $package->method('getName')->willReturn($name);

        $version = $this->createStub(SimpleVersion::class);
        $version->method('__toString')->willReturn($versionName);

        $dependency = $this->createStub(Dependency::class);
        $dependency->method('getPackage')->willReturn($package);
        $dependency->method('getVersion')->willReturn($version);

        return $dependency;
    }

    private function createDependencyDiff(string $name, string $before, string $after, int $type): DependencyDiff
    {
        $package = $this->createStub(Package::class);
        $package->method('getName')->willReturn($name);

        $beforeVersion = $this->createStub(SimpleVersion::class);
        $beforeVersion->method('__toString')->willReturn($before);

        $afterVersion = $this->createStub(SimpleVersion::class);
        $afterVersion->method('__toString')->willReturn($after);

        $versionDiff = $this->createStub(VersionDiff::class);
        $versionDiff->method('isCompatible')->willReturn(false);
        $versionDiff->method('getType')->willReturn($type);
        $versionDiff->method('getBefore')->willReturn($beforeVersion);
        $versionDiff->method('getAfter')->willReturn($afterVersion);

        $diff = $this->createStub(DependencyDiff::class);
        $diff->method('getPackage')->willReturn($package);
        $diff->method('getVersionDiff')->willReturn($versionDiff);

        return $diff;
    }
}
