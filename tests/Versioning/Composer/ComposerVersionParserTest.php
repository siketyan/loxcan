<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\Composer;

use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Versioning\Unknown\UnknownVersion;

class ComposerVersionParserTest extends TestCase
{
    private ComposerVersionParser $parser;

    protected function setUp(): void
    {
        $this->parser = new ComposerVersionParser();
    }

    public function test(): void
    {
        $version = $this->parser->parse('v1.2.3-dev123');
        $this->assertSame(1, $version->getX());
        $this->assertSame(2, $version->getY());
        $this->assertSame(3, $version->getZ());
        $this->assertSame(ComposerVersion::STABILITY_DEV, $version->getStability());
        $this->assertSame(123, $version->getNumber());

        $version = $this->parser->parse('4.3.2');
        $this->assertSame(4, $version->getX());
        $this->assertSame(3, $version->getY());
        $this->assertSame(2, $version->getZ());
        $this->assertSame(ComposerVersion::STABILITY_STABLE, $version->getStability());
        $this->assertSame(0, $version->getNumber());
    }

    public function testUnsupported(): void
    {
        $this->assertInstanceOf(
            UnknownVersion::class,
            $this->parser->parse('v1.2.3_not_supported'),
        );
    }
}
