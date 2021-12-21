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
        /* @var ComposerVersion $version */
        $version = $this->parser->parse('v1.2.3-dev123', 'hash');
        $this->assertSame(1, $version->getX());
        $this->assertSame(2, $version->getY());
        $this->assertSame(3, $version->getZ());
        $this->assertSame(ComposerVersion::STABILITY_DEV, $version->getStability());
        $this->assertSame(123, $version->getNumber());
        $this->assertSame('hash', $version->getHash());

        /* @var ComposerVersion $version */
        $version = $this->parser->parse('4.3.2', 'hash');
        $this->assertSame(4, $version->getX());
        $this->assertSame(3, $version->getY());
        $this->assertSame(2, $version->getZ());
        $this->assertSame(ComposerVersion::STABILITY_STABLE, $version->getStability());
        $this->assertSame(0, $version->getNumber());
        $this->assertSame('hash', $version->getHash());

        /* @var ComposerVersion $version */
        $version = $this->parser->parse('dev-branch', 'hash');
        $this->assertSame('dev-branch', $version->getBranch());
        $this->assertSame('hash', $version->getHash());
    }

    public function testUnsupported(): void
    {
        $this->assertInstanceOf(
            UnknownVersion::class,
            $this->parser->parse('v1.2.3_not_supported', 'hash'),
        );
    }
}
