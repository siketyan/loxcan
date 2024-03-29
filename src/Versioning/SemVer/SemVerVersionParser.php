<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\SemVer;

use Siketyan\Loxcan\Versioning\Unknown\UnknownVersion;

class SemVerVersionParser
{
    private const PATTERN = /* @lang text */
        '/^(\d+)\.(\d+)\.(\d+)(?:\-(?<pre>[A-Za-z0-9\-\.]+))?(?:\+(?<build>[A-Za-z0-9\-\.]+))?$/';

    public function parse(string $version): SemVerVersion|UnknownVersion
    {
        if (!preg_match(self::PATTERN, $version, $matches)) {
            return new UnknownVersion($version);
        }

        $preRelease = [];
        $build = [];

        if (\array_key_exists('pre', $matches)) {
            foreach (explode('.', $matches['pre']) as $identifier) {
                $preRelease[] = is_numeric($identifier) ? (int) $identifier : $identifier;
            }
        }

        if (\array_key_exists('build', $matches)) {
            foreach (explode('.', $matches['build']) as $identifier) {
                $build[] = is_numeric($identifier) ? (int) $identifier : $identifier;
            }
        }

        return new SemVerVersion(
            (int) $matches[1],
            (int) $matches[2],
            (int) $matches[3],
            array_filter($preRelease, fn ($i): bool => $i !== ''),
            array_filter($build, fn ($i): bool => $i !== ''),
        );
    }
}
