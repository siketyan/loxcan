<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\Composer;

use Composer\Semver\VersionParser as SemverVersionParser;
use Siketyan\Loxcan\Versioning\Unknown\UnknownVersion;

class ComposerVersionParser
{
    public function __construct(
        private readonly SemverVersionParser $parser,
    ) {
    }

    public function parse(string $version, string $hash): ComposerVersion|UnknownVersion
    {
        if (str_starts_with($version, 'dev-')) {
            return new ComposerVersion(
                'dev-' . $hash,
                $version,
                0,
                0,
                0,
                $hash,
                $version,
            );
        }

        try {
            $normalized = $this->parser->normalize($version);
        } catch (\UnexpectedValueException) {
            return new UnknownVersion($version);
        }

        $parts = explode('.', explode('-', $normalized)[0]);

        return new ComposerVersion(
            $normalized,
            $version,
            (int) ($parts[0] ?? 0),
            (int) ($parts[1] ?? 0),
            (int) ($parts[2] ?? 0),
            $hash,
        );
    }
}
