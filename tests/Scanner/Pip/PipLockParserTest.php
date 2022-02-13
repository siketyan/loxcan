<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Pip;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\Simple\SimpleVersion;
use Siketyan\Loxcan\Versioning\Simple\SimpleVersionParser;

class PipLockParserTest extends TestCase
{
    use ProphecyTrait;

    private const CONTENTS = <<<'EOS'
        {
            "default": {
                "@foo/bar": {
                    "version": "==1.2.3.4"
                }
            },
            "develop": {
                "baz": {
                    "version": "==3.2.1"
                }
            }
        }
        EOS;

    private ObjectProphecy $packagePool;
    private ObjectProphecy $versionParser;
    private PipLockParser $parser;

    protected function setUp(): void
    {
        $this->packagePool = $this->prophesize(PipPackagePool::class);
        $this->versionParser = $this->prophesize(SimpleVersionParser::class);

        $this->parser = new PipLockParser(
            $this->packagePool->reveal(),
            $this->versionParser->reveal(),
        );
    }

    public function test(): void
    {
        $cache = $this->prophesize(Package::class)->reveal();
        $fooBarVersion = $this->prophesize(SimpleVersion::class)->reveal();
        $barBazVersion = $this->prophesize(SimpleVersion::class)->reveal();

        $this->packagePool->get('@foo/bar')->willReturn(null);
        $this->packagePool->get('baz')->willReturn($cache);
        $this->packagePool->add(Argument::type(Package::class))->shouldBeCalledOnce();

        $this->versionParser->parse('1.2.3.4')->willReturn($fooBarVersion);
        $this->versionParser->parse('3.2.1')->willReturn($barBazVersion);

        $collection = $this->parser->parse(self::CONTENTS);
        $dependencies = $collection->getDependencies();

        $this->assertCount(2, $dependencies);
        $this->assertContainsOnlyInstancesOf(Dependency::class, $dependencies);

        $this->assertSame('@foo/bar', $dependencies[0]->getPackage()->getName());
        $this->assertSame($fooBarVersion, $dependencies[0]->getVersion());

        $this->assertSame($cache, $dependencies[1]->getPackage());
        $this->assertSame($barBazVersion, $dependencies[1]->getVersion());
    }
}
