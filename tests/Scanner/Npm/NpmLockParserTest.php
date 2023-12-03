<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Npm;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\SemVer\SemVerVersion;
use Siketyan\Loxcan\Versioning\SemVer\SemVerVersionParser;

class NpmLockParserTest extends TestCase
{
    private const CONTENTS = <<<'EOS'
        {
            "packages": {
                "@foo/bar": {
                    "version": "1.2.3-dev"
                },
                "baz": {
                    "version": "3.2.1"
                }
            }
        }
        EOS;

    private MockObject&NpmPackagePool $packagePool;
    private MockObject&SemVerVersionParser $versionParser;
    private NpmLockParser $parser;

    protected function setUp(): void
    {
        $this->packagePool = $this->createMock(NpmPackagePool::class);
        $this->versionParser = $this->createMock(SemVerVersionParser::class);

        $this->parser = new NpmLockParser(
            $this->packagePool,
            $this->versionParser,
        );
    }

    public function test(): void
    {
        $cache = $this->createStub(Package::class);
        $fooBarVersion = $this->createStub(SemVerVersion::class);
        $barBazVersion = $this->createStub(SemVerVersion::class);

        $this->packagePool->method('get')->willReturnCallback(fn (string $name) => match ($name) {
            '@foo/bar' => null,
            'baz' => $cache,
            default => $this->fail('unexpected pattern'),
        });

        $this->packagePool->expects($this->once())->method('add')->with($this->isInstanceOf(Package::class));

        $this->versionParser->method('parse')->willReturnMap([
            ['1.2.3-dev', $fooBarVersion],
            ['3.2.1', $barBazVersion],
        ]);

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
