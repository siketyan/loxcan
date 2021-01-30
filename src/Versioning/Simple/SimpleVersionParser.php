<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\Simple;

use Siketyan\Loxcan\Versioning\Unknown\UnknownVersion;
use Siketyan\Loxcan\Versioning\VersionInterface;

class SimpleVersionParser
{
    private const PATTERN = '/^(\d+)\.(\d+)\.(\d+)(?:\.(\d+))?$/';

    public function parse(string $version): VersionInterface
    {
        if (!preg_match(self::PATTERN, $version, $matches)) {
            return new UnknownVersion($version);
        }

        return new SimpleVersion(
            (int) $matches[1],
            (int) $matches[2],
            (int) $matches[3],
            count($matches) > 4 ? (int) $matches[4] : null,
        );
    }
}
