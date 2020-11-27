<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter\GitHub;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollectionDiff;
use Siketyan\Loxcan\Model\DependencyDiff;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\Simple\SimpleVersion;
use Siketyan\Loxcan\Versioning\VersionDiff;

class GitHubMarkdownBuilderTest extends TestCase
{
    use ProphecyTrait;

    private GitHubMarkdownBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new GitHubMarkdownBuilder();
    }

    public function test(): void
    {
        $filename = 'foo.lock';

        $diff = $this->prophesize(DependencyCollectionDiff::class);
        $diff->getAdded()->willReturn([$this->createDependency('added', 'v1.2.3')]);
        $diff->getRemoved()->willReturn([$this->createDependency('removed', 'v3.2.1')]);
        $diff->getUpdated()->willReturn([
            $this->createDependencyDiff('upgraded', 'v1.1.1', 'v2.2.2', VersionDiff::UPGRADED),
            $this->createDependencyDiff('downgraded', 'v4.4.4', 'v3.3.3', VersionDiff::DOWNGRADED),
            $this->createDependencyDiff('unknown', 'v5.5.5', 'v5.5.5', VersionDiff::UNKNOWN),
        ]);

        $markdown = $this->builder->build($diff->reveal(), $filename);

        $this->assertSame(
            <<<'EOS'
#### foo.lock
||Package|Before|After|
|---|---|---|---|
|➕|added||v1.2.3|
|⬆️|upgraded|v1.1.1|v2.2.2|
|⬇️|downgraded|v4.4.4|v3.3.3|
|🔄|unknown|v5.5.5|v5.5.5|
|➖|removed|v3.2.1||
EOS,
            $markdown,
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
        $versionDiff->getType()->willReturn($type);
        $versionDiff->getBefore()->willReturn($beforeVersion->reveal());
        $versionDiff->getAfter()->willReturn($afterVersion->reveal());

        $diff = $this->prophesize(DependencyDiff::class);
        $diff->getPackage()->willReturn($package->reveal());
        $diff->getVersionDiff()->willReturn($versionDiff->reveal());

        return $diff->reveal();
    }
}
