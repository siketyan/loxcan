<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Composer;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Model\Version;

class ComposerLockParserTest extends TestCase
{
    use ProphecyTrait;

    private const CONTENTS = <<<'EOS'
{
    "packages": [
        {
            "name": "foo/bar",
            "version": "v1.2.3.4"
        }
    ],
    "packages-dev": [
        {
            "name": "bar/baz",
            "version": "3.2.1"
        }
    ]
}
EOS;

    private ObjectProphecy $packagePool;
    private ComposerLockParser $parser;

    protected function setUp(): void
    {
        $this->packagePool = $this->prophesize(ComposerPackagePool::class);

        $this->parser = new ComposerLockParser(
            $this->packagePool->reveal(),
        );
    }

    public function test(): void
    {
        $cache = $this->prophesize(Package::class)->reveal();

        $this->packagePool->get('foo/bar')->willReturn(null);
        $this->packagePool->get('bar/baz')->willReturn($cache);
        $this->packagePool->add(Argument::type(Package::class))->shouldBeCalledOnce();

        $collection = $this->parser->parse(self::CONTENTS);
        $dependencies = $collection->getDependencies();

        $this->assertCount(2, $dependencies);
        $this->assertContainsOnlyInstancesOf(Dependency::class, $dependencies);

        $this->assertSame('foo/bar', $dependencies[0]->getPackage()->getName());
        $this->assertVersion(1, 2, 3, 4, $dependencies[0]->getVersion());

        $this->assertSame($cache, $dependencies[1]->getPackage());
        $this->assertVersion(3, 2, 1, null, $dependencies[1]->getVersion());
    }

    private function assertVersion(int $major, int $minor, int $patch, ?int $revision, Version $actual): void
    {
        $this->assertSame($major, $actual->getMajor());
        $this->assertSame($minor, $actual->getMinor());
        $this->assertSame($patch, $actual->getPatch());
        $this->assertSame($revision, $actual->getRevision());
    }
}
