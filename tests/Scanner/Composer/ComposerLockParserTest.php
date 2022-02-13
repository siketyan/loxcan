<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Composer;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\Composer\ComposerVersion;
use Siketyan\Loxcan\Versioning\Composer\ComposerVersionParser;

class ComposerLockParserTest extends TestCase
{
    use ProphecyTrait;

    private const CONTENTS = <<<'EOS'
        {
            "packages": [
                {
                    "name": "foo/bar",
                    "version": "v1.2.3.4",
                    "dist": {
                        "reference": "hash"
                    }
                }
            ],
            "packages-dev": [
                {
                    "name": "bar/baz",
                    "version": "3.2.1",
                    "dist": {
                        "reference": "hash"
                    }
                }
            ]
        }
        EOS;

    private ObjectProphecy $packagePool;
    private ObjectProphecy $versionParser;
    private ComposerLockParser $parser;

    protected function setUp(): void
    {
        $this->packagePool = $this->prophesize(ComposerPackagePool::class);
        $this->versionParser = $this->prophesize(ComposerVersionParser::class);

        $this->parser = new ComposerLockParser(
            $this->packagePool->reveal(),
            $this->versionParser->reveal(),
        );
    }

    public function test(): void
    {
        $cache = $this->prophesize(Package::class)->reveal();
        $fooBarVersion = $this->prophesize(ComposerVersion::class)->reveal();
        $barBazVersion = $this->prophesize(ComposerVersion::class)->reveal();

        $this->packagePool->get('foo/bar')->willReturn(null);
        $this->packagePool->get('bar/baz')->willReturn($cache);
        $this->packagePool->add(Argument::type(Package::class))->shouldBeCalledOnce();

        $this->versionParser->parse('v1.2.3.4', 'hash')->willReturn($fooBarVersion);
        $this->versionParser->parse('3.2.1', 'hash')->willReturn($barBazVersion);

        $collection = $this->parser->parse(self::CONTENTS);
        $dependencies = $collection->getDependencies();

        $this->assertCount(2, $dependencies);
        $this->assertContainsOnlyInstancesOf(Dependency::class, $dependencies);

        $this->assertSame('foo/bar', $dependencies[0]->getPackage()->getName());
        $this->assertSame($fooBarVersion, $dependencies[0]->getVersion());

        $this->assertSame($cache, $dependencies[1]->getPackage());
        $this->assertSame($barBazVersion, $dependencies[1]->getVersion());
    }
}
