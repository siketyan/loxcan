<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Cargo;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\SemVer\SemVerVersion;
use Siketyan\Loxcan\Versioning\SemVer\SemVerVersionParser;

class CargoLockParserTest extends TestCase
{
    use ProphecyTrait;

    private const CONTENTS = <<<'EOS'
        [[package]]
        name = "foo/bar"
        version = "1.2.3-dev"

        [[package]]
        name = "bar/baz"
        version = "3.2.1"
        EOS;

    /**
     * @var ObjectProphecy<CargoPackagePool>
     */
    private ObjectProphecy $packagePool;

    /**
     * @var ObjectProphecy<SemVerVersionParser>
     */
    private ObjectProphecy $versionParser;

    private CargoLockParser $parser;

    protected function setUp(): void
    {
        $this->packagePool = $this->prophesize(CargoPackagePool::class);
        $this->versionParser = $this->prophesize(SemVerVersionParser::class);

        $this->parser = new CargoLockParser(
            $this->packagePool->reveal(),
            $this->versionParser->reveal(),
        );
    }

    public function test(): void
    {
        $cache = $this->prophesize(Package::class)->reveal();
        $fooBarVersion = $this->prophesize(SemVerVersion::class)->reveal();
        $barBazVersion = $this->prophesize(SemVerVersion::class)->reveal();

        $this->packagePool->get('foo/bar')->willReturn(null);
        $this->packagePool->get('bar/baz')->willReturn($cache);
        $this->packagePool->add(Argument::type(Package::class))->shouldBeCalledOnce();

        $this->versionParser->parse('1.2.3-dev')->willReturn($fooBarVersion);
        $this->versionParser->parse('3.2.1')->willReturn($barBazVersion);

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
