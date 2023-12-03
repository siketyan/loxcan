<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Pnpm;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\SemVer\SemVerVersion;
use Siketyan\Loxcan\Versioning\SemVer\SemVerVersionParser;

class PnpmLockParserTest extends TestCase
{
    private MockObject&PnpmPackagePool $packagePool;
    private MockObject&SemVerVersionParser $versionParser;
    private PnpmLockParser $parser;

    protected function setUp(): void
    {
        $this->packagePool = $this->createMock(PnpmPackagePool::class);
        $this->versionParser = $this->createMock(SemVerVersionParser::class);

        $this->parser = new PnpmLockParser(
            $this->packagePool,
            $this->versionParser,
        );
    }

    #[DataProvider('provideCases')]
    public function test(string $yaml): void
    {
        $cache = $this->createStub(Package::class);
        $fooVersion = $this->createStub(SemVerVersion::class);
        $barVersion = $this->createStub(SemVerVersion::class);

        $this->packagePool->method('get')->willReturnCallback(fn (string $name): ?Stub => match ($name) {
            'foo' => null,
            'bar' => $cache,
            default => $this->fail('unexpected pattern'),
        });

        $this->packagePool->expects($this->once())->method('add')->with($this->isInstanceOf(Package::class));

        $this->versionParser->method('parse')->willReturnMap([
            ['1.2.3-dev', $fooVersion],
            ['3.2.1', $barVersion],
        ]);

        $collection = $this->parser->parse($yaml);
        $dependencies = $collection->getDependencies();

        $this->assertCount(2, $dependencies);
        $this->assertContainsOnlyInstancesOf(Dependency::class, $dependencies);

        $this->assertSame('foo', $dependencies[0]->getPackage()->getName());
        $this->assertSame($fooVersion, $dependencies[0]->getVersion());

        $this->assertSame($cache, $dependencies[1]->getPackage());
        $this->assertSame($barVersion, $dependencies[1]->getVersion());
    }

    /**
     * @return \Iterator<string, array{0: string}>
     */
    public static function provideCases(): \Iterator
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
