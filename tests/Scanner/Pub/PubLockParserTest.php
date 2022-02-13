<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Pub;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\SemVer\SemVerVersion;
use Siketyan\Loxcan\Versioning\SemVer\SemVerVersionParser;

class PubLockParserTest extends TestCase
{
    use ProphecyTrait;

    private const CONTENTS = <<<'EOS'
        packages:
          foo:
            version: "1.2.3-dev"
          bar:
            version: "3.2.1"
        EOS;

    private ObjectProphecy $packagePool;
    private ObjectProphecy $versionParser;
    private PubLockParser $parser;

    protected function setUp(): void
    {
        $this->packagePool = $this->prophesize(PubPackagePool::class);
        $this->versionParser = $this->prophesize(SemVerVersionParser::class);

        $this->parser = new PubLockParser(
            $this->packagePool->reveal(),
            $this->versionParser->reveal(),
        );
    }

    public function test(): void
    {
        $cache = $this->prophesize(Package::class)->reveal();
        $fooBarVersion = $this->prophesize(SemVerVersion::class)->reveal();
        $barBazVersion = $this->prophesize(SemVerVersion::class)->reveal();

        $this->packagePool->get('foo')->willReturn(null);
        $this->packagePool->get('bar')->willReturn($cache);
        $this->packagePool->add(Argument::type(Package::class))->shouldBeCalledOnce();

        $this->versionParser->parse('1.2.3-dev')->willReturn($fooBarVersion);
        $this->versionParser->parse('3.2.1')->willReturn($barBazVersion);

        $collection = $this->parser->parse(self::CONTENTS);
        $dependencies = $collection->getDependencies();

        $this->assertCount(2, $dependencies);
        $this->assertContainsOnlyInstancesOf(Dependency::class, $dependencies);

        $this->assertSame('foo', $dependencies[0]->getPackage()->getName());
        $this->assertSame($fooBarVersion, $dependencies[0]->getVersion());

        $this->assertSame($cache, $dependencies[1]->getPackage());
        $this->assertSame($barBazVersion, $dependencies[1]->getVersion());
    }
}
