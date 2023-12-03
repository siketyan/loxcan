<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Pub;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\SemVer\SemVerVersion;
use Siketyan\Loxcan\Versioning\SemVer\SemVerVersionParser;

class PubLockParserTest extends TestCase
{
    private const CONTENTS = <<<'EOS'
        packages:
          foo:
            version: "1.2.3-dev"
          bar:
            version: "3.2.1"
        EOS;

    private MockObject&PubPackagePool $packagePool;
    private MockObject&SemVerVersionParser $versionParser;
    private PubLockParser $parser;

    protected function setUp(): void
    {
        $this->packagePool = $this->createMock(PubPackagePool::class);
        $this->versionParser = $this->createMock(SemVerVersionParser::class);

        $this->parser = new PubLockParser(
            $this->packagePool,
            $this->versionParser,
        );
    }

    public function test(): void
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

        $collection = $this->parser->parse(self::CONTENTS);
        $dependencies = $collection->getDependencies();

        $this->assertCount(2, $dependencies);
        $this->assertContainsOnlyInstancesOf(Dependency::class, $dependencies);

        $this->assertSame('foo', $dependencies[0]->getPackage()->getName());
        $this->assertSame($fooVersion, $dependencies[0]->getVersion());

        $this->assertSame($cache, $dependencies[1]->getPackage());
        $this->assertSame($barVersion, $dependencies[1]->getVersion());
    }
}
