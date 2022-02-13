<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\Simple;

use Siketyan\Loxcan\Versioning\Unknown\UnknownVersion;

class SimpleVersionParser
{
    private const PATTERN = '/^(\d+)\.(\d+)(?:\.(\d+)(?:\.(\d+))?)?$/';

    public function parse(string $version): SimpleVersion|UnknownVersion
    {
        if (!preg_match(self::PATTERN, $version, $matches)) {
            return new UnknownVersion($version);
        }

        return new SimpleVersion(
            (int) $matches[1],
            (int) $matches[2],
            count($matches) > 3 ? (int) $matches[3] : null,
            count($matches) > 4 ? (int) $matches[4] : null,
        );
    }
}
