<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\Composer;

use Siketyan\Loxcan\Exception\UnsupportedVersionException;

class ComposerVersionParser
{
    private const PATTERN = '/^v?(\d+)\.(\d+)\.(\d+)(?:-(dev|alpha|beta|RC)(\d+)?)?$/';

    public function parse(string $version): ComposerVersion
    {
        if (!preg_match(self::PATTERN, $version, $matches)) {
            throw new UnsupportedVersionException($version);
        }

        return new ComposerVersion(
            (int) $matches[1],
            (int) $matches[2],
            (int) $matches[3],
            count($matches) > 4 ? ComposerVersion::STABILITIES[$matches[4]] : ComposerVersion::STABILITY_STABLE,
            count($matches) > 5 ? (int) $matches[5] : 0,
        );
    }
}
