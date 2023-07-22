<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Pnpm;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\SemVer\SemVerVersion;
use Siketyan\Loxcan\Versioning\SemVer\SemVerVersionParser;

class PnpmLockParserTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<PnpmPackagePool>
     */
    private ObjectProphecy $packagePool;

    /**
     * @var ObjectProphecy<SemVerVersionParser>
     */
    private ObjectProphecy $versionParser;

    private PnpmLockParser $parser;

    protected function setUp(): void
    {
        $this->packagePool = $this->prophesize(PnpmPackagePool::class);
        $this->versionParser = $this->prophesize(SemVerVersionParser::class);

        $this->parser = new PnpmLockParser(
            $this->packagePool->reveal(),
            $this->versionParser->reveal(),
        );
    }

    /**
     * @dataProvider provideCases
     */
    public function test(string $yaml): void
    {
        $cache = $this->prophesize(Package::class)->reveal();
        $fooBarVersion = $this->prophesize(SemVerVersion::class)->reveal();
        $barBazVersion = $this->prophesize(SemVerVersion::class)->reveal();

        $this->packagePool->get('foo', Argument::any())->willReturn(null);
        $this->packagePool->get('bar', Argument::any())->willReturn($cache);
        $this->packagePool->add(Argument::type(Package::class))->shouldBeCalledOnce();

        $this->versionParser->parse('1.2.3-dev')->willReturn($fooBarVersion);
        $this->versionParser->parse('3.2.1')->willReturn($barBazVersion);

        $collection = $this->parser->parse($yaml);
        $dependencies = $collection->getDependencies();

        $this->assertCount(2, $dependencies);
        $this->assertContainsOnlyInstancesOf(Dependency::class, $dependencies);

        $this->assertSame('foo', $dependencies[0]->getPackage()->getName());
        $this->assertSame($fooBarVersion, $dependencies[0]->getVersion());

        $this->assertSame($cache, $dependencies[1]->getPackage());
        $this->assertSame($barBazVersion, $dependencies[1]->getVersion());
    }

    /**
     * @return \Iterator<string, array{0: string}>
     */
    public function provideCases(): \Iterator
    {
        yield 'simple version pattern' => [
            <<<'EOS'
                dependencies:
                  foo: 1.2.3-dev

                devDependencies:
                  bar: 3.2.1
                EOS,
        ];

        yield 'nested version pattern' => [
            <<<'EOS'
                dependencies:
                  foo:
                    specification: ^1.0.0-dev
                    version: 1.2.3-dev

                devDependencies:
                  bar:
                    specification: ^3.0.0
                    version: 3.2.1(foo@1.2.3-dev)(baz@4.5.6)
                EOS,
        ];
    }
}
